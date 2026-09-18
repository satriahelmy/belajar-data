# Datasets

Versioned learning datasets belong under `datasets/{dataset-key}/{version}/`.

Each version contains:

- `manifest.json`: machine key, version, grain, files, table schemas, and relationships;
- `data_dictionary.json`: learner-facing field definitions and curriculum usage;
- versioned data files and optional downloadable learning assets.

Dataset keys use lowercase kebab-case and versions use `v1`, `v2`, and so on. Dataset files are trusted repository assets; M0C adds no upload or remote datasource feature.
