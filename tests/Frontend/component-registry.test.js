import test from 'node:test';
import assert from 'node:assert/strict';
import { isRegisteredComponent, registeredComponentTypes } from '../../resources/js/components/registry.js';

test('the production registry exposes only approved browser components', () => {
    assert.deepEqual(registeredComponentTypes, ['practice', 'sql-playground', 'spreadsheet-playground', 'python-practice', 'visualization-playground', 'join-grain-playground', 'sampling-uncertainty-playground', 'metric-tree-builder', 'communication-builder']);
    assert.equal(isRegisteredComponent('practice'), true);
    assert.equal(isRegisteredComponent('sql-playground'), true);
    assert.equal(isRegisteredComponent('spreadsheet-playground'), true);
    assert.equal(isRegisteredComponent('python-practice'), true);
    assert.equal(isRegisteredComponent('visualization-playground'), true);
    assert.equal(isRegisteredComponent('join-grain-playground'), true);
    assert.equal(isRegisteredComponent('sampling-uncertainty-playground'), true);
    assert.equal(isRegisteredComponent('metric-tree-builder'), true);
    assert.equal(isRegisteredComponent('communication-builder'), true);
    assert.equal(isRegisteredComponent('python'), false);
    assert.equal(isRegisteredComponent('visualization'), false);
});
