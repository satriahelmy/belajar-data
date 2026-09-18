import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { pathToFileURL } from 'node:url';
import initSqlJs from 'sql.js';
import { SQL_SPIKE_QUERY_CASES } from '../../resources/js/spike/sql/query-cases.js';
import { validateSqlResult } from '../../resources/js/spike/sql/result-validator.js';

const fixtureRoot = path.resolve('datasets/nusamart/v1/sql');
const manifest = JSON.parse(fs.readFileSync(path.join(fixtureRoot, 'manifest.json'), 'utf8'));

function sqlType(type) {
    if (type === 'integer') return 'INTEGER';
    if (type === 'number') return 'REAL';
    return 'TEXT';
}

function quoteIdentifier(value) {
    return `"${value.replaceAll('"', '""')}"`;
}

test('sql.js executes the representative Module 03 query suite', async () => {
    const SQL = await initSqlJs({
        locateFile: (file) => pathToFileURL(path.join('node_modules/sql.js/dist', file)).href,
    });
    const db = new SQL.Database();

    for (const [tableName, table] of Object.entries(manifest.tables)) {
        const rows = JSON.parse(fs.readFileSync(path.join(fixtureRoot, manifest.files[tableName]), 'utf8'));
        const columns = Object.entries(table.columns)
            .map(([name, type]) => `${quoteIdentifier(name)} ${sqlType(type)}`)
            .join(', ');
        db.run(`CREATE TABLE ${quoteIdentifier(tableName)} (${columns});`);
        const columnNames = Object.keys(table.columns);
        const statement = db.prepare(
            `INSERT INTO ${quoteIdentifier(tableName)} (${columnNames.map(quoteIdentifier).join(', ')}) VALUES (${columnNames.map(() => '?').join(', ')});`,
        );
        rows.forEach((row) => statement.run(columnNames.map((column) => row[column] ?? null)));
        statement.free();
    }

    for (const queryCase of SQL_SPIKE_QUERY_CASES) {
        const result = db.exec(queryCase.sql)[0] ?? { columns: [], values: [] };
        const validation = validateSqlResult({ columns: result.columns, rows: result.values }, queryCase.expected, queryCase.options);
        assert.equal(validation.passed, true, `failed query case: ${queryCase.id}`);
    }

    db.close();
});
