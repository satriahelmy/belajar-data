# Content

Repository-first instructional content belongs under `content/`.

The current production contract is:

```text
content/{path-key}/
├── path.json
└── {module-key}/
    ├── module.json
    └── {topic-key}/
        ├── lesson.md
        └── exercises.json
```

Keys are lowercase kebab-case and stable once published. Display titles belong in metadata and may change without changing routes or references.

`lesson.md` contains narrative Markdown and only registered structured directives. `exercises.json` contains machine-addressable exercise configurations and validators; it never contains executable JavaScript.
