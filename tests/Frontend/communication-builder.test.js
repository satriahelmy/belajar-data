import test from 'node:test';
import assert from 'node:assert/strict';
import { buildCommunicationAnswer, fieldsComplete } from '../../resources/js/components/communication-builder.js';

const order = ['headline', 'evidence', 'known', 'unknown', 'next_step'];

const fields = {
    headline: 'Revenue naik dari Agustus ke September.',
    evidence: 'Revenue naik dari 450 menjadi 600.',
    known: 'Ada perbedaan revenue antarperiode.',
    unknown: 'Penyebab kenaikan belum terbukti.',
    next_step: 'Bandingkan orders dan average order value.',
};

test('communication builder detects complete structured fields', () => {
    assert.equal(fieldsComplete(fields, order), true);
    assert.equal(fieldsComplete({ ...fields, unknown: '' }, order), false);
});

test('communication answer preserves bounded fields and checklist state', () => {
    assert.deepEqual(buildCommunicationAnswer(fields, [true, true, true, true, true]), {
        text: JSON.stringify(fields),
        fields,
        checklist: [true, true, true, true, true],
        complete: true,
    });
    assert.equal(buildCommunicationAnswer(fields, [true, false, true, true, true]).complete, false);
});

test('communication answer never marks incomplete fields as complete', () => {
    const answer = buildCommunicationAnswer({ ...fields, evidence: '  ' }, [true, true, true, true, true]);

    assert.equal(answer.complete, false);
    assert.equal(answer.fields.evidence, '');
});
