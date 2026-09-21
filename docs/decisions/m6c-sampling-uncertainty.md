# M6C: Sampling and Uncertainty Decision

**Status:** M6C representative interactive implemented

## Decision

Use a bounded, deterministic browser simulation backed by the canonical `nusamart/v1` fixture. The `sampling-uncertainty-playground` component exposes only predefined metrics, sample sizes, repeat counts, and seeds. It renders repeated sample means, the population mean, and the minimum and maximum estimate for the selected state.

No new statistics or simulation package is added. The browser and PHP fixture use the same small deterministic shuffle algorithm so that the server fallback, browser view, and result tests describe the same sample sequence.

## Evidence demonstrated

- Server-rendered lesson and challenge content are loaded from Module 07 repository files.
- The fallback table remains readable before JavaScript enhancement.
- Browser controls are finite and config-driven: `revenue` or `quantity`, sample sizes 3, 5, or 8, repeats 3, 5, or 10, and seeds 7 or 42.
- The same metric, size, repeats, and seed produce the same estimates in PHP and browser tests.
- Result validation persists only the bounded selection answer through the existing M2 attempt contract.
- Reset returns to the configured starter state and authenticated completion can be loaded again.
- The ordinary Module 01 lesson does not load the sampling component.

## Alternatives considered

### General Monte Carlo or statistics library

Rejected for this slice. It would add dependency and API surface without improving the learning object, which only needs repeated bounded sample means.

### Unseeded browser randomness

Rejected because repeated results would not be reproducible for assessment, debugging, or content validation.

### Server-side sampling execution

Rejected because the approved architecture keeps learning simulations in the browser and keeps the shared-hosting deployment conventional. PHP is used only for the server-rendered fallback, fixture contract, and validation boundary.

### User-uploaded or arbitrary data simulation

Rejected because it would expand the component into a general statistics tool and introduce data validation, privacy, and performance concerns outside M6C.

## Contract and boundaries

- Markdown references only `:::sampling-practice id="..."`.
- The server resolves the id to a registered `sampling_uncertainty` result-based exercise.
- The exercise must reference `nusamart/v1` and use the finite allowlists above.
- The answer contract is a single table row with `metric`, `sample_size`, `repeats`, and `seed`.
- No arbitrary JavaScript, uploaded data, free-form formulas, or unbounded repeat/sample controls are accepted.

## Cache and validation

Sampling lesson cache keys include the source hash of the canonical fixture. A fixture change therefore invalidates rendered sampling output. `content:validate` checks the lesson directive, exercise config, dataset reference, and dataset manifest before deployment. Feature and frontend tests cover deterministic summaries, bounded answer validation, cache invalidation, registry boundaries, fallback rendering, and ordinary-lesson isolation.

## Known limitations

This is a representative learning slice, not a full statistics library. It does not estimate confidence intervals, model sampling bias, or accept arbitrary populations. Those concepts can be taught with additional bounded content later if the curriculum requires them.
