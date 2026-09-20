export function matchesResultBasedAnswer(answer, validator) {
    if (! Array.isArray(answer?.columns) || ! Array.isArray(answer?.rows)) {
        return false;
    }

    if (! answer.columns.every((column) => typeof column === 'string')) {
        return false;
    }

    const expectedColumns = validator?.expected_columns ?? [];
    const requiredColumns = validator?.required_columns ?? [];

    if (JSON.stringify(answer.columns) !== JSON.stringify(expectedColumns)
        || requiredColumns.some((column) => ! answer.columns.includes(column))) {
        return false;
    }

    const expectedRows = validator?.expected_rows ?? [];
    if (answer.rows.length !== expectedRows.length
        || ! answer.rows.every((row) => Array.isArray(row))) {
        return false;
    }

    let rows = answer.rows;
    let expected = expectedRows;
    if ((validator?.row_order ?? 'ordered') === 'unordered') {
        rows = sortRows(rows);
        expected = sortRows(expected);
    }

    return expected.every((expectedRow, rowIndex) => {
        const actualRow = rows[rowIndex];
        if (! Array.isArray(expectedRow) || actualRow.length !== expectedRow.length) {
            return false;
        }

        return expectedRow.every((expectedValue, columnIndex) => cellMatches(
            expectedValue,
            actualRow[columnIndex],
            validator,
        ));
    });
}

function sortRows(rows) {
    return [...rows].sort((left, right) => JSON.stringify(left).localeCompare(JSON.stringify(right)));
}

function cellMatches(expected, actual, validator) {
    if (expected === null || actual === null) {
        if ((validator?.null_behavior ?? 'strict') === 'empty_as_null') {
            return (expected === null && (actual === null || actual === ''))
                || (actual === null && (expected === null || expected === ''));
        }

        return expected === actual;
    }

    if (isNumeric(expected) && isNumeric(actual)) {
        return Math.abs(Number(expected) - Number(actual)) <= Number(validator?.numeric_tolerance ?? 0);
    }

    return String(expected).trim().toLowerCase() === String(actual).trim().toLowerCase();
}

function isNumeric(value) {
    return (typeof value === 'number' && Number.isFinite(value))
        || (typeof value === 'string' && value.trim() !== '' && Number.isFinite(Number(value)));
}
