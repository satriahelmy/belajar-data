import test from 'node:test';
import assert from 'node:assert/strict';
import { buildVisualizationAnswer, buildVisualizationView } from '../../resources/js/components/visualization-playground.js';

const fixture = {
    rows: [
        { order_id: 'O-1', month: '2025-08', category: 'Electronics', region: 'West', channel: 'Online', quantity: 2, revenue: 240 },
        { order_id: 'O-2', month: '2025-09', category: 'Home', region: 'East', channel: 'Store', quantity: 1, revenue: 120 },
    ],
    views: {
        category: { rows: [
            { label: 'Electronics', revenue: 240, quantity: 2, orders: 1, average_order_value: 240 },
            { label: 'Home', revenue: 120, quantity: 1, orders: 1, average_order_value: 120 },
        ] },
    },
};

test('visualization view produces bounded category table and chart data', () => {
    const view = buildVisualizationView(fixture, {
        metric: 'revenue',
        dimension: 'category',
        chart: 'bar',
        sort: 'descending',
        highlight: 'top',
        scaleMode: 'honest',
    });

    assert.deepEqual(view.columns, ['category', 'revenue', 'orders']);
    assert.deepEqual(view.rows, [['Electronics', 240, 1], ['Home', 120, 1]]);
    assert.equal(view.chartType, 'bar');
});

test('visualization answer contains only the finite selection contract', () => {
    assert.deepEqual(buildVisualizationAnswer({
        chart: 'composition',
        metric: 'revenue',
        dimension: 'month',
        sort: 'chronological',
        highlight: 'none',
        scaleMode: 'honest',
    }), {
        columns: ['chart', 'metric', 'dimension', 'sort', 'highlight', 'scale_mode'],
        rows: [['composition', 'revenue', 'month', 'chronological', 'none', 'honest']],
    });
});

test('visualization view keeps bounded representations available for histogram, composition, and distribution', () => {
    const extendedFixture = {
        ...fixture,
        views: {
            ...fixture.views,
            month: { rows: [
                { label: '2025-08', revenue: 240, quantity: 2, orders: 1, average_order_value: 240 },
                { label: '2025-09', revenue: 120, quantity: 1, orders: 1, average_order_value: 120 },
            ] },
        },
    };

    const histogram = buildVisualizationView(extendedFixture, {
        metric: 'revenue', dimension: 'category', chart: 'histogram', sort: 'descending', highlight: 'none', scaleMode: 'honest',
    });
    const composition = buildVisualizationView(extendedFixture, {
        metric: 'orders', dimension: 'month', chart: 'composition', sort: 'chronological', highlight: 'none', scaleMode: 'honest',
    });
    const distribution = buildVisualizationView(extendedFixture, {
        metric: 'average_order_value', dimension: 'category', chart: 'distribution', sort: 'descending', highlight: 'none', scaleMode: 'honest',
    });

    assert.equal(histogram.chartType, 'bar');
    assert.deepEqual(composition.columns, ['month', 'Electronics', 'Home']);
    assert.deepEqual(composition.rows, [['2025-08', 1, 0], ['2025-09', 0, 1]]);
    assert.equal(distribution.rows[2][0], 'Median');
});
