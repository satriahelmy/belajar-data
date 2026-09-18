function valuesEqual(actual, expected, tolerance) {
    if (actual === null || expected === null) {
        return actual === expected;
    }

    if (typeof actual === 'number' && typeof expected === 'number') {
        return Math.abs(actual - expected) <= tolerance;
    }

    return actual === expected;
}

function rowsEqual(actual, expected, tolerance) {
    return actual.length === expected.length
        && actual.every((row, rowIndex) => row.length === expected[rowIndex].length
            && row.every((value, columnIndex) => valuesEqual(value, expected[rowIndex][columnIndex], tolerance)));
}

export function validateSqlResult(actual, expected, options = {}) {
    const tolerance = options.numericTolerance ?? 0;
    const columnsMatch = JSON.stringify(actual.columns) === JSON.stringify(expected.columns);
    const actualRows = expected.ordered ? actual.rows : [...actual.rows].sort((a, b) => JSON.stringify(a).localeCompare(JSON.stringify(b)));
    const expectedRows = expected.ordered ? expected.rows : [...expected.rows].sort((a, b) => JSON.stringify(a).localeCompare(JSON.stringify(b)));

    return {
        passed: columnsMatch && rowsEqual(actualRows, expectedRows, tolerance),
        checks: {
            columns: columnsMatch,
            rows: rowsEqual(actualRows, expectedRows, tolerance),
            ordered: Boolean(expected.ordered),
            numericTolerance: tolerance,
        },
    };
}
