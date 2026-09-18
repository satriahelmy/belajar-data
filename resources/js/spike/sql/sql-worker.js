import initSqlJs from 'sql.js';
import wasmUrl from 'sql.js/dist/sql-wasm.wasm?url';
import { assertReadOnlyQuery } from './query-policy.js';

let database = null;
let fixture = null;

function quoteIdentifier(value) {
    return `"${String(value).replaceAll('"', '""')}"`;
}

function quoteValue(value) {
    if (value === null || value === undefined) {
        return 'NULL';
    }

    if (typeof value === 'number') {
        return Number.isFinite(value) ? String(value) : 'NULL';
    }

    return `'${String(value).replaceAll("'", "''")}'`;
}

function sqliteType(type) {
    if (type === 'integer') return 'INTEGER';
    if (type === 'number') return 'REAL';
    return 'TEXT';
}

async function initializeDatabase(tables) {
    const startedAt = performance.now();
    const SQL = await initSqlJs({ locateFile: () => wasmUrl });
    database = new SQL.Database();
    fixture = tables;

    for (const [tableName, table] of Object.entries(tables)) {
        const columns = Object.entries(table.columns)
            .map(([name, type]) => `${quoteIdentifier(name)} ${sqliteType(type)}`)
            .join(', ');
        database.run(`CREATE TABLE ${quoteIdentifier(tableName)} (${columns});`);

        const columnNames = Object.keys(table.columns);
        const placeholders = columnNames.map(() => '?').join(', ');
        const statement = database.prepare(
            `INSERT INTO ${quoteIdentifier(tableName)} (${columnNames.map(quoteIdentifier).join(', ')}) VALUES (${placeholders});`,
        );

        for (const row of table.rows) {
            statement.run(columnNames.map((column) => row[column] ?? null));
        }

        statement.free();
    }

    return {
        initializationMs: performance.now() - startedAt,
        tableCount: Object.keys(tables).length,
        rowCount: Object.values(tables).reduce((sum, table) => sum + table.rows.length, 0),
    };
}

function runQuery(sql, maxRows) {
    const startedAt = performance.now();
    assertReadOnlyQuery(sql);
    const result = database.exec(sql)[0] ?? { columns: [], values: [] };
    const values = result.values ?? [];

    return {
        columns: result.columns ?? [],
        rows: values.slice(0, maxRows),
        truncated: values.length > maxRows,
        rowCount: values.length,
        queryMs: performance.now() - startedAt,
    };
}

self.addEventListener('message', async (event) => {
    const { id, action, payload } = event.data;

    try {
        if (action === 'init') {
            self.postMessage({ id, action, result: await initializeDatabase(payload.tables) });
            return;
        }

        if (action === 'query') {
            self.postMessage({ id, action, result: runQuery(payload.sql, payload.maxRows) });
            return;
        }

        if (action === 'fixture-info') {
            self.postMessage({ id, action, result: { tableCount: Object.keys(fixture ?? {}).length } });
            return;
        }

        throw new Error(`Unknown worker action [${action}].`);
    } catch (error) {
        self.postMessage({ id, action, error: error instanceof Error ? error.message : String(error) });
    }
});
