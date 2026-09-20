import test from 'node:test';
import assert from 'node:assert/strict';
import { isRegisteredComponent, registeredComponentTypes } from '../../resources/js/components/registry.js';

test('the production registry exposes only approved browser components', () => {
    assert.deepEqual(registeredComponentTypes, ['practice', 'sql-playground', 'spreadsheet-playground']);
    assert.equal(isRegisteredComponent('practice'), true);
    assert.equal(isRegisteredComponent('sql-playground'), true);
    assert.equal(isRegisteredComponent('spreadsheet-playground'), true);
    assert.equal(isRegisteredComponent('python'), false);
    assert.equal(isRegisteredComponent('visualization'), false);
});
