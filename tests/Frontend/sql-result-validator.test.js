import test from 'node:test';
import assert from 'node:assert/strict';
import { validateSqlResult } from '../../resources/js/spike/sql/result-validator.js';

test('SQL result validation supports columns, unordered rows, tolerance, and NULL', () => {
    const validation = validateSqlResult(
        {
            columns: ['metric', 'previous'],
            rows: [[10.0000001, null], [5, 10]],
        },
        {
            columns: ['metric', 'previous'],
            rows: [[5, 10], [10, null]],
            ordered: false,
        },
        { numericTolerance: 0.000001 },
    );

    assert.equal(validation.passed, true);
    assert.equal(validation.checks.columns, true);
    assert.equal(validation.checks.rows, true);
});

test('SQL result validation rejects an unexpected column or value', () => {
    const validation = validateSqlResult(
        { columns: ['metric'], rows: [[11]] },
        { columns: ['metric'], rows: [[10]], ordered: true },
    );

    assert.equal(validation.passed, false);
    assert.equal(validation.checks.rows, false);
});
