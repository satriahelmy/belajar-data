# Datasets domain

Dataset metadata is repository-first and versioned. `DatasetManifestRepository` validates manifest/data-dictionary contracts and file references before content is deployed.

Future browser execution adapters must consume a validated manifest and bounded fixture; they must not connect to the application database.
