# Projects domain

Project definitions and stage content remain repository-owned under
`content/projects`. This boundary contains project loading and the independent
learner project-progress store. Project progress uses stable project/stage keys;
it does not mirror the full content model into MySQL.
