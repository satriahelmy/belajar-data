const ERROR_CODES = Object.freeze({
    value: '#VALUE!',
    ref: '#REF!',
    name: '#NAME?',
    na: '#N/A',
    div0: '#DIV/0!',
    duplicate: '#DUPLICATE!',
});

function isError(value) {
    return typeof value === 'string' && /^#(?:VALUE!|REF!|NAME\?|N\/A|DIV\/0!|DUPLICATE!)$/.test(value);
}

function isRange(value) {
    return value && value.kind === 'range';
}

function flatten(value) {
    if (isRange(value)) {
        return value.values.flat();
    }

    return [value];
}

function columnIndex(label) {
    let result = 0;
    for (const character of label) {
        result = result * 26 + character.charCodeAt(0) - 64;
    }
    return result - 1;
}

function columnLabel(index) {
    let value = index + 1;
    let label = '';
    while (value > 0) {
        const remainder = (value - 1) % 26;
        label = String.fromCharCode(65 + remainder) + label;
        value = Math.floor((value - 1) / 26);
    }
    return label;
}

function parseReference(reference) {
    const match = /^(?:(?<sheet>[A-Za-z][A-Za-z0-9_]*)!)?(?<column>[A-Z]{1,3})(?<row>[1-9][0-9]*)$/i.exec(reference);
    if (! match) {
        return null;
    }

    return {
        sheet: match.groups.sheet ?? 'Analysis',
        column: match.groups.column.toUpperCase(),
        row: Number(match.groups.row),
    };
}

function parseRangeReference(reference) {
    const [start, end] = reference.split(':');
    const left = parseReference(start);
    const right = parseReference(end ?? start);

    if (! left || ! right || (left.sheet !== right.sheet && right.sheet !== 'Analysis')) {
        return null;
    }

    return {
        sheet: left.sheet,
        startColumn: columnIndex(left.column),
        endColumn: columnIndex(right.column),
        startRow: left.row,
        endRow: right.row,
    };
}

function resolveSheetName(sheets, requested) {
    return Object.keys(sheets).find((name) => name.toLowerCase() === requested.toLowerCase()) ?? null;
}

function tokenize(source) {
    const tokens = [];
    let index = 0;

    while (index < source.length) {
        const character = source[index];
        if (/\s/.test(character)) {
            index += 1;
            continue;
        }

        if (character === '"' || character === "'") {
            const quote = character;
            let value = '';
            index += 1;
            while (index < source.length && source[index] !== quote) {
                if (source[index] === '\\' && source[index + 1] === quote) {
                    value += quote;
                    index += 2;
                } else {
                    value += source[index++];
                }
            }
            if (source[index] !== quote) throw new Error(ERROR_CODES.value);
            index += 1;
            tokens.push({ type: 'string', value });
            continue;
        }

        const number = /^(?:\d+(?:\.\d*)?|\.\d+)/.exec(source.slice(index));
        if (number) {
            tokens.push({ type: 'number', value: Number(number[0]) });
            index += number[0].length;
            continue;
        }

        const identifier = /^[A-Za-z_][A-Za-z0-9_!]*/.exec(source.slice(index));
        if (identifier) {
            tokens.push({ type: 'identifier', value: identifier[0] });
            index += identifier[0].length;
            continue;
        }

        const operator = /^(>=|<=|<>|=|>|<|\+|-|\*|\/|\(|\)|,|:)/.exec(source.slice(index));
        if (operator) {
            tokens.push({ type: 'operator', value: operator[0] });
            index += operator[0].length;
            continue;
        }

        throw new Error(ERROR_CODES.value);
    }

    tokens.push({ type: 'eof', value: null });
    return tokens;
}

function sheetRowValue(sheet, rowNumber, columnNumber) {
    if (! sheet) return ERROR_CODES.ref;
    if (rowNumber === 1) return sheet.columnNames[columnNumber] ?? ERROR_CODES.ref;
    const row = sheet.rows[rowNumber - 2];
    if (! row || columnNumber < 0 || columnNumber >= sheet.columnNames.length) return ERROR_CODES.ref;
    return row[sheet.columnNames[columnNumber]] ?? null;
}

function createRange(sheet, reference) {
    const values = [];
    const rows = [];

    for (let rowNumber = reference.startRow; rowNumber <= reference.endRow; rowNumber += 1) {
        const row = [];
        for (let columnNumber = reference.startColumn; columnNumber <= reference.endColumn; columnNumber += 1) {
            row.push(sheetRowValue(sheet, rowNumber, columnNumber));
        }
        rows.push(row);
        values.push(...row);
    }

    return {
        kind: 'range',
        values,
        rows,
        width: reference.endColumn - reference.startColumn + 1,
        height: reference.endRow - reference.startRow + 1,
    };
}

function truthy(value) {
    if (isError(value)) return false;
    if (value === null || value === '' || value === false) return false;
    return Number(value) !== 0 || value === true || typeof value === 'string';
}

function compareValues(left, operator, right) {
    if (isError(left)) return left;
    if (isError(right)) return right;

    const leftNumber = typeof left === 'number' ? left : Number(left);
    const rightNumber = typeof right === 'number' ? right : Number(right);
    const numeric = left !== '' && right !== '' && Number.isFinite(leftNumber) && Number.isFinite(rightNumber);
    const a = numeric ? leftNumber : String(left ?? '').toLowerCase();
    const b = numeric ? rightNumber : String(right ?? '').toLowerCase();

    switch (operator) {
        case '=': return a === b;
        case '<>': return a !== b;
        case '>': return a > b;
        case '<': return a < b;
        case '>=': return a >= b;
        case '<=': return a <= b;
        default: return ERROR_CODES.value;
    }
}

function criteriaMatches(value, criteria) {
    if (isError(criteria)) return false;
    const text = String(criteria ?? '');
    const match = /^(>=|<=|<>|=|>|<)(.*)$/.exec(text);
    const operator = match?.[1] ?? '=';
    const expected = match ? match[2] : criteria;
    return compareValues(value, operator, expected) === true;
}

function evaluateFunction(name, args) {
    const upper = name.toUpperCase();
    const values = args.flatMap(flatten);
    if (values.some(isError)) return values.find(isError);

    if (upper === 'SUM') return values.filter((value) => typeof value === 'number').reduce((sum, value) => sum + value, 0);
    if (upper === 'COUNT') return values.filter((value) => typeof value === 'number').length;
    if (upper === 'AVERAGE') {
        const numbers = values.filter((value) => typeof value === 'number');
        return numbers.length ? numbers.reduce((sum, value) => sum + value, 0) / numbers.length : ERROR_CODES.div0;
    }

    if (upper === 'IF') return args.length === 3 ? (truthy(args[0]) ? args[1] : args[2]) : ERROR_CODES.value;

    if (upper === 'COUNTIF') {
        const range = args[0];
        if (! isRange(range) || args.length !== 2) return ERROR_CODES.value;
        return range.values.filter((value) => criteriaMatches(value, args[1])).length;
    }

    if (upper === 'COUNTIFS') {
        if (args.length < 2 || args.length % 2 !== 0) return ERROR_CODES.value;
        const ranges = [];
        for (let index = 0; index < args.length; index += 2) {
            if (! isRange(args[index])) return ERROR_CODES.value;
            ranges.push([args[index], args[index + 1]]);
        }
        const length = ranges[0][0].values.length;
        if (ranges.some(([range]) => range.values.length !== length)) return ERROR_CODES.value;
        return Array.from({ length }, (_, index) => index)
            .filter((index) => ranges.every(([range, criteria]) => criteriaMatches(range.values[index], criteria)))
            .length;
    }

    if (upper === 'SUMIF') {
        const criteriaRange = args[0];
        const sumRange = args[2] ?? criteriaRange;
        if (! isRange(criteriaRange) || ! isRange(sumRange) || args.length < 2 || criteriaRange.values.length !== sumRange.values.length) return ERROR_CODES.value;
        return criteriaRange.values.reduce((sum, value, index) => criteriaMatches(value, args[1]) && typeof sumRange.values[index] === 'number' ? sum + sumRange.values[index] : sum, 0);
    }

    if (upper === 'SUMIFS') {
        if (args.length < 3 || args.length % 2 !== 1 || ! isRange(args[0])) return ERROR_CODES.value;
        const sumRange = args[0];
        const pairs = [];
        for (let index = 1; index < args.length; index += 2) {
            if (! isRange(args[index]) || args[index].values.length !== sumRange.values.length) return ERROR_CODES.value;
            pairs.push([args[index], args[index + 1]]);
        }
        return sumRange.values.reduce((sum, value, index) => pairs.every(([range, criteria]) => criteriaMatches(range.values[index], criteria)) && typeof value === 'number' ? sum + value : sum, 0);
    }

    if (upper === 'VLOOKUP') {
        const lookupValue = args[0];
        const table = args[1];
        const index = args[2];
        const exact = args[3];
        if (! isRange(table) || ! Number.isInteger(index) || index < 1 || index > table.width || (exact !== false && exact !== 0)) return ERROR_CODES.value;
        const matches = table.rows.filter((row) => row[0] === lookupValue);
        if (matches.length > 1) return ERROR_CODES.duplicate;
        if (matches.length === 0) return ERROR_CODES.na;
        return matches[0][index - 1];
    }

    return ERROR_CODES.name;
}

class FormulaParser {
    constructor(source, context) {
        this.tokens = tokenize(source.startsWith('=') ? source.slice(1) : source);
        this.context = context;
        this.position = 0;
    }

    current() { return this.tokens[this.position]; }
    consume(value) {
        if (value && this.current().value !== value) throw new Error(ERROR_CODES.value);
        return this.tokens[this.position++];
    }

    parse() {
        const value = this.parseComparison();
        if (this.current().type !== 'eof') throw new Error(ERROR_CODES.value);
        return value;
    }

    parseComparison() {
        let value = this.parseAdditive();
        while (['=', '<>', '>', '<', '>=', '<='].includes(this.current().value)) {
            const operator = this.consume().value;
            value = compareValues(value, operator, this.parseAdditive());
        }
        return value;
    }

    parseAdditive() {
        let value = this.parseMultiplicative();
        while (['+', '-'].includes(this.current().value)) {
            const operator = this.consume().value;
            const right = this.parseMultiplicative();
            if (isError(value)) return value;
            if (isError(right) || typeof value !== 'number' || typeof right !== 'number') return ERROR_CODES.value;
            value = operator === '+' ? value + right : value - right;
        }
        return value;
    }

    parseMultiplicative() {
        let value = this.parseUnary();
        while (['*', '/'].includes(this.current().value)) {
            const operator = this.consume().value;
            const right = this.parseUnary();
            if (isError(value)) return value;
            if (isError(right) || typeof value !== 'number' || typeof right !== 'number') return ERROR_CODES.value;
            if (operator === '/' && right === 0) return ERROR_CODES.div0;
            value = operator === '*' ? value * right : value / right;
        }
        return value;
    }

    parseUnary() {
        if (this.current().value === '-') {
            this.consume('-');
            const value = this.parseUnary();
            return typeof value === 'number' ? -value : ERROR_CODES.value;
        }
        if (this.current().value === '+') {
            this.consume('+');
            return this.parseUnary();
        }
        return this.parsePrimary();
    }

    parsePrimary() {
        const token = this.current();
        if (token.type === 'number' || token.type === 'string') {
            this.consume();
            return token.value;
        }
        if (token.value === '(') {
            this.consume('(');
            const value = this.parseComparison();
            this.consume(')');
            return value;
        }
        if (token.type !== 'identifier') throw new Error(ERROR_CODES.value);

        const identifier = this.consume().value;
        if (this.current().value === '(') {
            this.consume('(');
            const args = [];
            if (this.current().value !== ')') {
                do {
                    args.push(this.parseComparison());
                    if (this.current().value !== ',') break;
                    this.consume(',');
                } while (true);
            }
            this.consume(')');
            return evaluateFunction(identifier, args);
        }

        if (this.current().value === ':') {
            this.consume(':');
            const end = this.consume().value;
            const range = parseRangeReference(`${identifier}:${end}`);
            const sheetName = range ? resolveSheetName(this.context.sheets, range.sheet) : null;
            if (! range || ! sheetName) return ERROR_CODES.ref;
            range.sheet = sheetName;
            return createRange(this.context.sheets[range.sheet], range);
        }

        if (identifier.toUpperCase() === 'TRUE') return true;
        if (identifier.toUpperCase() === 'FALSE') return false;

        const reference = parseReference(identifier);
        if (reference?.sheet === 'Analysis') {
            return this.context.analysisCells[`${reference.column}${reference.row}`] ?? ERROR_CODES.ref;
        }
        if (reference) {
            const sheetName = resolveSheetName(this.context.sheets, reference.sheet);
            if (sheetName) {
                return sheetRowValue(this.context.sheets[sheetName], reference.row, columnIndex(reference.column));
            }
        }
        return ERROR_CODES.name;
    }
}

export function evaluateFormula(formula, context) {
    try {
        if (typeof formula !== 'string' || ! formula.trim().startsWith('=')) return formula;
        return new FormulaParser(formula.trim(), context).parse();
    } catch (error) {
        return error instanceof Error && /^#/.test(error.message) ? error.message : ERROR_CODES.value;
    }
}

export function calculateFormulaCells(cells, fixture) {
    const sheets = Object.fromEntries(Object.entries(fixture.tables).map(([name, table]) => [name, {
        columnNames: Object.keys(table.columns),
        rows: table.rows,
    }]));
    const analysisCells = {};
    const values = {};

    for (const [cell, formula] of Object.entries(cells)) {
        const trimmed = typeof formula === 'string' ? formula.trim() : formula;
        values[cell] = typeof trimmed === 'string' && trimmed.startsWith('=')
            ? evaluateFormula(trimmed, { sheets, analysisCells })
            : trimmed === '' || trimmed === null
                ? null
                : (typeof trimmed === 'string' && Number.isFinite(Number(trimmed)) ? Number(trimmed) : trimmed);
        analysisCells[cell] = values[cell];
    }

    return { values, sheets };
}

export function applyTableView(rows, options = {}) {
    const region = options.region ?? 'all';
    const month = options.month ?? 'all';
    const sort = options.sort ?? 'order_id_asc';
    const filtered = rows.filter((row) => (region === 'all' || row.region === region)
        && (month === 'all' || row.order_date.startsWith(month)));

    const direction = sort.endsWith('_desc') ? -1 : 1;
    const field = sort.replace(/_(?:asc|desc)$/, '');
    filtered.sort((left, right) => {
        const a = left[field];
        const b = right[field];
        if (a === b) return 0;
        return (a > b ? 1 : -1) * direction;
    });

    return filtered;
}

export function buildConfiguredSummary(fixture, measures = ['revenue', 'quantity', 'order_count']) {
    const products = fixture.tables.products.rows;
    const productMap = new Map();
    for (const product of products) {
        if (productMap.has(product.product_id)) return { error: ERROR_CODES.duplicate, rows: [] };
        productMap.set(product.product_id, product);
    }

    const groups = new Map();
    for (const transaction of fixture.tables.transactions.rows) {
        const category = productMap.get(transaction.product_id)?.category ?? ERROR_CODES.na;
        if (isError(category)) return { error: category, rows: [] };
        if (! groups.has(category)) groups.set(category, { category, revenue: 0, quantity: 0, orders: new Set() });
        const group = groups.get(category);
        group.revenue += Number(transaction.revenue) || 0;
        group.quantity += Number(transaction.quantity) || 0;
        group.orders.add(transaction.order_id);
    }

    return {
        columns: ['category', ...measures],
        rows: [...groups.values()].sort((a, b) => a.category.localeCompare(b.category)).map((group) => [
            group.category,
            ...measures.map((measure) => measure === 'order_count' ? group.orders.size : group[measure]),
        ]),
    };
}

export { ERROR_CODES };
