export function assertReadOnlyQuery(sql) {
    const normalized = sql
        .replace(/\/\*[\s\S]*?\*\//g, ' ')
        .replace(/--[^\r\n]*/g, ' ')
        .trim();

    if (! /^(SELECT|WITH)\b/i.test(normalized)) {
        throw new Error('Only SELECT and WITH queries are allowed in this spike.');
    }

    if (/\b(ATTACH|DETACH|PRAGMA|VACUUM|REINDEX|ANALYZE|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER|REPLACE|LOAD_EXTENSION)\b/i.test(normalized)) {
        throw new Error('The query contains a blocked operation.');
    }
}
