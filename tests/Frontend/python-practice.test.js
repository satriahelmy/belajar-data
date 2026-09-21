import assert from 'node:assert/strict';
import test from 'node:test';
import { evaluateBoundedSource } from '../../resources/js/components/python-practice.js';

const config = {
    limits: { max_source_characters: 8000 },
    required_operations: ['read_csv', 'merge', 'groupby', 'sum'],
    validator: {
        expected_columns: ['category', 'revenue'],
        expected_rows: [['Electronics', 560], ['Grocery', 190], ['Home', 300]],
    },
};

test('bounded Python fallback accepts the required analytical steps and returns deterministic output', () => {
    const result = evaluateBoundedSource(
        'pd.read_csv("transactions.csv")\nsales.merge(products, on="product_id")\nsales.groupby("category")["revenue"].sum()',
        config,
    );

    assert.equal(result.ok, true);
    assert.deepEqual(result.answer, {
        columns: ['category', 'revenue'],
        rows: [['Electronics', 560], ['Grocery', 190], ['Home', 300]],
    });
});

test('bounded Python fallback rejects missing or unsafe operations without executing source', () => {
    const missing = evaluateBoundedSource('pd.read_csv("transactions.csv")', config);
    assert.equal(missing.ok, false);
    assert.match(missing.error, /merge/);

    const unsafe = evaluateBoundedSource('import os\nos.system("whoami")', config);
    assert.equal(unsafe.ok, false);
    assert.match(unsafe.error, /dataset yang disediakan/);

    const tooLong = evaluateBoundedSource('x'.repeat(8001), config);
    assert.equal(tooLong.ok, false);
    assert.match(tooLong.error, /terlalu panjang/);
});
