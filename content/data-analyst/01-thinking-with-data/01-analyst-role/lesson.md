BelajarData starts with the question, not the tool. A Data Analyst turns an ambiguous business concern into a question that data can help answer.

## A simple workflow

The workflow is iterative:

1. Clarify the business question.
2. Understand the data and its grain.
3. Analyze and validate the result.
4. Communicate what the evidence supports.

| Step | Output |
| --- | --- |
| Question | A measurable analytical question |
| Data | Tables, fields, and definitions |
| Analysis | A reproducible calculation |
| Evidence | Findings with context and limits |

```sql
SELECT category, SUM(revenue) AS revenue
FROM orders
GROUP BY category;
```

Read the [BelajarData learning path](/learn) for the wider learning path.

:::callout type="common-mistake"
Do not jump from a pattern in transaction data to a causal explanation without supporting evidence.
:::

:::practice type="multiple_choice" id="thinking-analyst-01"
:::
