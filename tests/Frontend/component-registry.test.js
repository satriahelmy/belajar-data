import test from 'node:test';
import assert from 'node:assert/strict';
import { isRegisteredComponent, registeredComponentTypes } from '../../resources/js/components/registry.js';

test('the production registry exposes only lightweight registered components', () => {
    assert.deepEqual(registeredComponentTypes, ['practice']);
    assert.equal(isRegisteredComponent('practice'), true);
    assert.equal(isRegisteredComponent('python'), false);
    assert.equal(isRegisteredComponent('visualization'), false);
});
