# Application domain boundaries

Application domain code is organized under `App\Domain` and remains inside the Laravel monolith.

- `Learning` — curriculum delivery and learning content concerns.
- `Learner` — learner state, attempts, bookmarks, and account-facing concerns.
- `Projects` — project definitions and learner project progress.
- `Datasets` — dataset metadata and version references.

M0A establishes the boundaries only; domain behavior is implemented in later milestones.
