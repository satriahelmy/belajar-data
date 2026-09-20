function valuesEqual(actual, expected, tolerance) {
    if (actual === null || expected === null) return actual === expected;
    if (typeof actual === 'number' && typeof expected === 'number') return Math.abs(actual - expected) <= tolerance;
    return actual === expected;
}

function sortRows(rows) {
    return [...rows].sort((left, right) => JSON.stringify(left).localeCompare(JSON.stringify(right)));
}

export function validateSpreadsheetResult(actual, expected, options = {}) {
    const tolerance = options.numericTolerance ?? 0;
    const actualRows = expected.ordered === false ? sortRows(actual.rows) : actual.rows;
    const expectedRows = expected.ordered === false ? sortRows(expected.rows) : expected.rows;
    const columns = JSON.stringify(actual.columns) === JSON.stringify(expected.columns);
    const rows = actualRows.length === expectedRows.length && actualRows.every((row, rowIndex) => row.length === expectedRows[rowIndex].length
        && row.every((value, columnIndex) => valuesEqual(value, expectedRows[rowIndex][columnIndex], tolerance)));

    return { passed: columns && rows, checks: { columns, rows, numericTolerance: tolerance } };
}
