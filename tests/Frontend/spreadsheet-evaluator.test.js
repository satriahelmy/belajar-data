import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import {
    applyTableView,
    buildConfiguredSummary,
    calculateFormulaCells,
    evaluateFormula,
    ERROR_CODES,
} from '../../resources/js/spreadsheet/evaluator.js';
import { validateSpreadsheetResult } from '../../resources/js/spreadsheet/result-validator.js';

const datasetRoot = path.resolve('datasets/nusamart/v1');
const manifest = JSON.parse(fs.readFileSync(path.join(datasetRoot, 'manifest.json'), 'utf8'));
const fixture = {
    dataset_key: manifest.dataset_key,
    version: manifest.version,
    grain: manifest.grain,
    tables: Object.fromEntries(['transactions', 'products'].map((name) => [name, {
        columns: manifest.tables[name].columns,
        rows: JSON.parse(fs.readFileSync(path.join(datasetRoot, manifest.files[name]), 'utf8')),
    }])),
};

test('bounded formula evaluator covers metrics, conditional logic, SUMIF, SUMIFS, and exact VLOOKUP', () => {
    const calculated = calculateFormulaCells({
        B2: '=SUM(Transactions!H2:H9)',
        B3: '=AVERAGE(Transactions!H2:H9)',
        B4: '=IF(B2>1000,"Above threshold","Check total")',
        B5: '=SUMIF(Transactions!E2:E9,"P-1001",Transactions!H2:H9)',
        B6: '=SUMIFS(Transactions!H2:H9,Transactions!C2:C9,"West")',
        B7: '=VLOOKUP("P-1001",Products!A2:C5,2,FALSE)',
        B8: '=COUNT(Transactions!H2:H9)',
        B9: '=COUNTIF(Transactions!C2:C9,"West")',
        B10: '=COUNTIFS(Transactions!C2:C9,"West",Transactions!D2:D9,"Online")',
        B11: '=B2-B6',
    }, fixture);

    assert.deepEqual(calculated.values, {
        B2: 1050,
        B3: 131.25,
        B4: 'Above threshold',
        B5: 480,
        B6: 710,
        B7: 'Electronics',
        B8: 8,
        B9: 5,
        B10: 3,
        B11: 340,
    });

    assert.deepEqual(calculateFormulaCells({ B12: '', B13: '120.00' }, fixture).values, { B12: null, B13: 120 });
    assert.equal(
        calculateFormulaCells({ B14: '=SUM(Transactions!H2:H5)+SUM(Transactions!H6:H9)' }, fixture).values.B14,
        1050,
    );
});

test('bounded evaluator preserves important spreadsheet errors', () => {
    const context = {
        sheets: Object.fromEntries(Object.entries(fixture.tables).map(([name, table]) => [name, {
            columnNames: Object.keys(table.columns),
            rows: table.rows,
        }])),
        analysisCells: {},
    };

    assert.equal(evaluateFormula('=VLOOKUP("P-9999",Products!A2:C5,2,FALSE)', context), ERROR_CODES.na);
    assert.equal(evaluateFormula('=1/0', context), ERROR_CODES.div0);
    assert.equal(evaluateFormula('=UnknownFunction(1)', context), ERROR_CODES.name);
    assert.equal(evaluateFormula('=Transactions!Z2', context), ERROR_CODES.ref);

    const duplicateFixture = {
        ...fixture,
        tables: {
            ...fixture.tables,
            products: {
                ...fixture.tables.products,
                rows: [...fixture.tables.products.rows, { product_id: 'P-1001', category: 'Other', brand: 'Duplicate' }],
            },
        },
    };
    const duplicateContext = {
        sheets: Object.fromEntries(Object.entries(duplicateFixture.tables).map(([name, table]) => [name, {
            columnNames: Object.keys(table.columns),
            rows: table.rows,
        }])),
        analysisCells: {},
    };
    assert.equal(evaluateFormula('=VLOOKUP("P-1001",Products!A2:C6,2,FALSE)', duplicateContext), ERROR_CODES.duplicate);
});

test('table view and configured summary remain bounded and deterministic', () => {
    const west = applyTableView(fixture.tables.transactions.rows, { region: 'West', sort: 'revenue_desc' });
    assert.deepEqual(west.map((row) => row.order_id), ['O-1001', 'O-1006', 'O-1008', 'O-1003', 'O-1004']);

    const summary = buildConfiguredSummary(fixture, ['revenue', 'quantity', 'order_count']);
    assert.deepEqual(summary.columns, ['category', 'revenue', 'quantity', 'order_count']);
    assert.deepEqual(summary.rows, [
        ['Electronics', 560, 5, 4],
        ['Grocery', 190, 7, 2],
        ['Home', 300, 3, 2],
    ]);
});

test('spreadsheet result validation is independent of formula spelling', () => {
    const validation = validateSpreadsheetResult(
        { columns: ['category', 'revenue'], rows: [['Home', 300.00001], ['Electronics', 560]] },
        { columns: ['category', 'revenue'], rows: [['Electronics', 560], ['Home', 300]], ordered: false },
        { numericTolerance: 0.001 },
    );

    assert.equal(validation.passed, true);
    assert.equal(validation.checks.columns, true);
    assert.equal(validation.checks.rows, true);
});
