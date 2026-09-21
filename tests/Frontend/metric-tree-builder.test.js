import test from 'node:test';
import assert from 'node:assert/strict';
import { buildMetricTreeAnswer, buildMetricTreeRows } from '../../resources/js/components/metric-tree-builder.js';

const nodes = [
    { id: 'revenue', label: 'Revenue', description: 'Metric utama.' },
    { id: 'orders', label: 'Orders', description: 'Jumlah order.' },
    { id: 'average-order-value', label: 'Average order value', description: 'Revenue per order.' },
    { id: 'units-per-order', label: 'Units per order', description: 'Unit per order.' },
    { id: 'average-price-per-unit', label: 'Average price per unit', description: 'Revenue per unit.' },
];

const correctParents = {
    orders: 'revenue',
    'average-order-value': 'revenue',
    'units-per-order': 'average-order-value',
    'average-price-per-unit': 'average-order-value',
};

test('metric tree rows preserve the predefined hierarchy', () => {
    assert.deepEqual(buildMetricTreeRows(nodes, 'revenue', correctParents).map((row) => [row.id, row.depth]), [
        ['revenue', 0],
        ['orders', 1],
        ['average-order-value', 1],
        ['units-per-order', 2],
        ['average-price-per-unit', 2],
    ]);
});

test('metric tree answer contains only bounded node relationships', () => {
    assert.deepEqual(buildMetricTreeAnswer(correctParents), {
        columns: ['node_id', 'parent_id'],
        rows: [
            ['orders', 'revenue'],
            ['average-order-value', 'revenue'],
            ['units-per-order', 'average-order-value'],
            ['average-price-per-unit', 'average-order-value'],
        ],
    });
});

test('metric tree rows do not recurse through a cycle', () => {
    const cyclic = { ...correctParents, orders: 'average-order-value', 'average-order-value': 'orders' };
    const rows = buildMetricTreeRows(nodes, 'revenue', cyclic);

    assert.deepEqual(rows.map((row) => row.id), ['revenue']);
});
