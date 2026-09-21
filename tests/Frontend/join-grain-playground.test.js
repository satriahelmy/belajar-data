import test from 'node:test';
import assert from 'node:assert/strict';
import { buildJoinAnswer, joinScenario } from '../../resources/js/components/join-grain-playground.js';

const fixture = {
    tables: {
        orders: { rows: [{ order_id: 'O-1' }, { order_id: 'O-2' }] },
        order_items: { rows: [
            { order_id: 'O-1', quantity: 1 },
            { order_id: 'O-1', quantity: 2 },
            { order_id: 'O-2', quantity: 1 },
        ] },
    },
};

test('join scenario exposes row multiplication and bounded preview rows', () => {
    const result = joinScenario(fixture, {
        left_table: 'orders',
        right_table: 'order_items',
        join_key: 'order_id',
    });

    assert.equal(result.left_rows, 2);
    assert.equal(result.right_rows, 3);
    assert.equal(result.result_rows, 3);
    assert.deepEqual(result.rows[0], ['O-1', 'O-1', 1]);
});

test('join answer contains only scenario and row-count evidence', () => {
    assert.deepEqual(buildJoinAnswer('order-to-items', { join_key: 'order_id' }, {
        left_rows: 6,
        right_rows: 9,
        result_rows: 9,
    }), {
        columns: ['scenario', 'join_key', 'left_rows', 'right_rows', 'result_rows'],
        rows: [['order-to-items', 'order_id', 6, 9, 9]],
    });
});
