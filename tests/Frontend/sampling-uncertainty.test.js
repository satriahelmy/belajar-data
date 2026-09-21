import test from 'node:test';
import assert from 'node:assert/strict';
import { buildSamplingAnswer, buildSamplingSummary } from '../../resources/js/components/sampling-uncertainty-playground.js';

const fixture = {
    rows: [
        { order_id: 'O-1', quantity: 2, revenue: 240 },
        { order_id: 'O-2', quantity: 1, revenue: 120 },
        { order_id: 'O-3', quantity: 3, revenue: 90 },
        { order_id: 'O-4', quantity: 1, revenue: 80 },
        { order_id: 'O-5', quantity: 1, revenue: 120 },
        { order_id: 'O-6', quantity: 2, revenue: 180 },
        { order_id: 'O-7', quantity: 4, revenue: 100 },
        { order_id: 'O-8', quantity: 1, revenue: 120 },
    ],
};

test('sampling summary is reproducible for the same bounded state', () => {
    const state = { metric: 'revenue', sampleSize: 3, repeats: 5, seed: 42 };
    const first = buildSamplingSummary(fixture, state);
    const second = buildSamplingSummary(fixture, state);

    assert.deepEqual(first, second);
    assert.equal(first.populationMean, 131.25);
    assert.equal(first.estimates.length, 5);
    assert.notEqual(first.estimateMin, first.estimateMax);
});

test('sampling summary changes through bounded sample controls', () => {
    const small = buildSamplingSummary(fixture, { metric: 'revenue', sampleSize: 3, repeats: 5, seed: 42 });
    const larger = buildSamplingSummary(fixture, { metric: 'revenue', sampleSize: 5, repeats: 10, seed: 7 });

    assert.notDeepEqual(small.estimates, larger.estimates);
    assert.equal(larger.estimates.length, 10);
    assert.equal(buildSamplingSummary(fixture, { metric: 'quantity', sampleSize: 3, repeats: 3, seed: 7 }).populationMean, 1.88);
});

test('sampling answer contains only the finite selection contract', () => {
    assert.deepEqual(buildSamplingAnswer({ metric: 'revenue', sampleSize: 3, repeats: 5, seed: 42 }), {
        columns: ['metric', 'sample_size', 'repeats', 'seed'],
        rows: [['revenue', 3, 5, 42]],
    });
});
