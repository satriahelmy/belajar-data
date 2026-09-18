import test from 'node:test';
import assert from 'node:assert/strict';
import { assertReadOnlyQuery } from '../../resources/js/spike/sql/query-policy.js';

test('SQL policy allows SELECT and CTE queries', () => {
    assert.doesNotThrow(() => assertReadOnlyQuery('WITH base AS (SELECT 1 AS value) SELECT value FROM base;'));
});

test('SQL policy blocks mutation, attachment, and pragma operations', () => {
    for (const sql of ['INSERT INTO orders VALUES (1)', 'ATTACH DATABASE \'remote.db\' AS remote', 'PRAGMA foreign_keys']) {
        assert.throws(() => assertReadOnlyQuery(sql));
    }
});
