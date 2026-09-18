export const SQL_SPIKE_QUERY_CASES = [
    {
        id: 'select-columns',
        label: 'SELECT + column selection',
        sql: 'SELECT order_id, customer_id FROM orders ORDER BY order_id LIMIT 3;',
        expected: {
            columns: ['order_id', 'customer_id'],
            rows: [['ORD001', 'C001'], ['ORD002', 'C002'], ['ORD003', 'C001']],
            ordered: true,
        },
    },
    {
        id: 'where',
        label: 'WHERE filter',
        sql: "SELECT order_id FROM orders WHERE channel = 'Online' ORDER BY order_id;",
        expected: {
            columns: ['order_id'],
            rows: [['ORD001'], ['ORD003'], ['ORD004'], ['ORD006']],
            ordered: true,
        },
    },
    {
        id: 'order-limit',
        label: 'ORDER BY + LIMIT',
        sql: 'SELECT order_id, shipping_cost FROM orders ORDER BY shipping_cost DESC LIMIT 2;',
        expected: {
            columns: ['order_id', 'shipping_cost'],
            rows: [['ORD004', 20], ['ORD002', 15]],
            ordered: true,
        },
    },
    {
        id: 'count',
        label: 'COUNT',
        sql: 'SELECT COUNT(*) AS item_rows FROM order_items;',
        expected: { columns: ['item_rows'], rows: [[9]], ordered: true },
    },
    {
        id: 'count-distinct',
        label: 'COUNT DISTINCT',
        sql: 'SELECT COUNT(DISTINCT order_id) AS order_count FROM order_items;',
        expected: { columns: ['order_count'], rows: [[6]], ordered: true },
    },
    {
        id: 'sum',
        label: 'SUM',
        sql: 'SELECT SUM(quantity * unit_price) AS revenue FROM order_items;',
        expected: { columns: ['revenue'], rows: [[1325]], ordered: true },
    },
    {
        id: 'avg',
        label: 'AVG',
        sql: 'SELECT AVG(unit_price) AS average_unit_price FROM order_items;',
        expected: { columns: ['average_unit_price'], rows: [[116.6666666667]], ordered: true },
        options: { numericTolerance: 0.000001 },
    },
    {
        id: 'group-by',
        label: 'GROUP BY + JOIN',
        sql: 'SELECT p.category, SUM(oi.quantity * oi.unit_price) AS revenue FROM order_items oi JOIN products p ON p.product_id = oi.product_id GROUP BY p.category ORDER BY p.category;',
        expected: {
            columns: ['category', 'revenue'],
            rows: [['Accessories', 175], ['Electronics', 550], ['Furniture', 600]],
            ordered: true,
        },
    },
    {
        id: 'multi-table-join',
        label: 'Multiple-table JOIN',
        sql: 'SELECT c.segment, COUNT(DISTINCT o.order_id) AS orders FROM orders o JOIN customers c ON c.customer_id = o.customer_id GROUP BY c.segment;',
        expected: {
            columns: ['segment', 'orders'],
            rows: [['Business', 3], ['Consumer', 3]],
            ordered: false,
        },
    },
    {
        id: 'case',
        label: 'CASE business logic',
        sql: "SELECT order_id, CASE WHEN SUM(quantity * unit_price) >= 300 THEN 'High Value' ELSE 'Standard' END AS order_band FROM order_items GROUP BY order_id ORDER BY order_id;",
        expected: {
            columns: ['order_id', 'order_band'],
            rows: [['ORD001', 'Standard'], ['ORD002', 'High Value'], ['ORD003', 'Standard'], ['ORD004', 'High Value'], ['ORD005', 'Standard'], ['ORD006', 'Standard']],
            ordered: true,
        },
    },
    {
        id: 'cte',
        label: 'CTE analysis structure',
        sql: "WITH monthly AS (SELECT substr(o.order_date, 1, 7) AS month, SUM(oi.quantity * oi.unit_price) AS revenue FROM orders o JOIN order_items oi ON oi.order_id = o.order_id GROUP BY month) SELECT month, revenue FROM monthly ORDER BY month;",
        expected: { columns: ['month', 'revenue'], rows: [['2025-08', 675], ['2025-09', 650]], ordered: true },
    },
    {
        id: 'date',
        label: 'Date extraction + comparison',
        sql: "SELECT order_id, substr(order_date, 1, 7) AS month FROM orders WHERE order_date >= '2025-09-01' ORDER BY order_date;",
        expected: { columns: ['order_id', 'month'], rows: [['ORD004', '2025-09'], ['ORD005', '2025-09'], ['ORD006', '2025-09']], ordered: true },
    },
    {
        id: 'window',
        label: 'Window function',
        sql: 'SELECT order_id, customer_id, ROW_NUMBER() OVER (PARTITION BY customer_id ORDER BY order_date) AS customer_order_number FROM orders ORDER BY order_id;',
        expected: {
            columns: ['order_id', 'customer_id', 'customer_order_number'],
            rows: [['ORD001', 'C001', 1], ['ORD002', 'C002', 1], ['ORD003', 'C001', 2], ['ORD004', 'C003', 1], ['ORD005', 'C004', 1], ['ORD006', 'C002', 2]],
            ordered: true,
        },
    },
    {
        id: 'lag',
        label: 'LAG time comparison',
        sql: "WITH monthly AS (SELECT substr(o.order_date, 1, 7) AS month, SUM(oi.quantity * oi.unit_price) AS revenue FROM orders o JOIN order_items oi ON oi.order_id = o.order_id GROUP BY month) SELECT month, revenue, LAG(revenue) OVER (ORDER BY month) AS previous_revenue, revenue - LAG(revenue) OVER (ORDER BY month) AS change FROM monthly ORDER BY month;",
        expected: { columns: ['month', 'revenue', 'previous_revenue', 'change'], rows: [['2025-08', 675, null, null], ['2025-09', 650, 675, -25]], ordered: true },
    },
    {
        id: 'join-multiplication',
        label: 'JOIN row multiplication (diagnostic)',
        sql: 'SELECT SUM(o.shipping_cost) AS inflated_shipping FROM orders o JOIN order_items oi ON oi.order_id = o.order_id;',
        expected: { columns: ['inflated_shipping'], rows: [[121]], ordered: true },
        note: 'The correct order-level total is 79. Joining before aggregating repeats order-level shipping for every item row.',
    },
];

export const SQL_SPIKE_LIMITS = Object.freeze({
    maxRows: 100,
    maxQueryCharacters: 10000,
    timeoutMs: 2000,
});
