import assert from 'node:assert/strict';
import test from 'node:test';

import { matchesResultBasedAnswer } from '../../resources/js/components/practice-validator.js';

const validator = {
    expected_columns: ['region', 'revenue'],
    required_columns: ['region'],
    expected_rows: [['South', 120], ['North', null]],
    row_order: 'unordered',
    numeric_tolerance: 0.5,
    null_behavior: 'strict',
};

test('browser result validation matches server ordering, tolerance, and null rules', () => {
    assert.equal(matchesResultBasedAnswer({
        columns: ['region', 'revenue'],
        rows: [['North', null], ['South', 120.4]],
    }, validator), true);
});

test('browser result validation rejects malformed rows and wrong columns', () => {
    assert.equal(matchesResultBasedAnswer({
        columns: ['region'],
        rows: [['North'], ['South']],
    }, validator), false);
    assert.equal(matchesResultBasedAnswer({
        columns: ['region', 'revenue'],
        rows: ['not-a-row', ['South', 120]],
    }, validator), false);
});
