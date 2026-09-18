# Learning domain

Learning-content delivery is repository-first. `ContentRepository` loads stable path/module/topic metadata, lesson Markdown, and structured exercise config. `MarkdownLessonRenderer` is the safe server-rendered boundary; `ComponentRegistry` is the allowlist for progressive learning blocks.

Future SQL, spreadsheet, Python, and visualization experiences belong behind registered, bounded adapters. They are not application-wide dependencies and are not enabled by M0C.
