# Tests

- `tests/Feature/` covers HTTP routes, server-rendered flows, database boundaries, and later browser-facing behavior.
- `tests/Unit/` covers isolated domain/configuration logic.
- `tests/Frontend/` covers dependency-free browser modules with Node's built-in test runner.

M0C includes content, dataset-manifest, component-registration, and rendering contract coverage. Tests should prove repository boundaries without turning the test suite into a second content authoring system.
