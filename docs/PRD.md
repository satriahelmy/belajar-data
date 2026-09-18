# BelajarData — Product Requirements Document (PRD)

**Product:** BelajarData  
**Version:** V1  
**Status:** Draft for implementation planning  
**Source of truth:** `curriculum.md`

---

## 1. Product Summary

BelajarData is an Indonesian guided learning platform for aspiring and developing Data Analysts.

The product is designed around a simple problem:

> Banyak orang ingin belajar data, tetapi bingung harus mulai dari mana, belajar apa dulu, bagaimana berlatih, dan kapan mereka sudah cukup memahami suatu kompetensi untuk lanjut.

BelajarData solves this through a structured learning journey that combines concise lessons, contextual practice, interactive exercises, professional tools, progress tracking, and applied projects.

### Positioning

> **Belajar data, tanpa bingung mulai dari mana.**

BelajarData is not intended to become a catalog of disconnected courses or a clone of professional data tools. It provides a coherent path from analytical thinking to applied business analysis.

### Core principles

1. **Guide, don't overwhelm.**
2. **Practice, don't memorize.**
3. **Curate, don't duplicate.**
4. **Tools are not the curriculum. Analysis is the curriculum.**
5. **Learn once, apply anywhere.**
6. **Start learning first. Set up tools when you need them.**
7. **Build enough interactivity to teach the concept. Don't rebuild the tool being taught.**

---

## 2. Problem

Data-analysis learning is commonly fragmented across tutorials, documentation, videos, courses, and tool-specific resources.

Learners frequently face several problems:

- they do not know what to learn first;
- they learn SQL, Python, spreadsheets, or BI tools independently without understanding how the skills connect;
- tutorials often emphasize syntax instead of analytical reasoning;
- practice frequently consists of isolated drills rather than business problems;
- learners can complete tutorials without knowing how to conduct an analysis independently;
- advanced material is often introduced before fundamentals are secure;
- professional tools can introduce setup friction before meaningful learning begins.

BelajarData should reduce this ambiguity by giving learners a recommended but flexible path and frequent opportunities to apply what they learn.

---

## 3. Product Goal

### Primary goal

Enable a learner to progress from basic analytical thinking to independently investigating and communicating a realistic business-data problem.

A successful learner should be able to:

- clarify a business question;
- understand tables, grain, metrics, and dimensions;
- manipulate data using spreadsheets, SQL, and Python/Pandas;
- identify and handle common data-quality problems;
- perform exploratory analysis;
- interpret basic statistical evidence responsibly;
- choose appropriate visual representations;
- define useful business metrics;
- organize metrics into monitoring/dashboard structures;
- implement analysis in Tableau;
- investigate business performance and potential drivers;
- distinguish evidence, interpretation, hypotheses, and unknowns;
- communicate findings and defensible next steps;
- combine these competencies in substantial projects.

### Product success principle

V1 succeeds if it provides a **complete, understandable, practice-oriented Data Analyst learning journey**, not if it has the largest feature set.

---

## 4. Target Users

### Primary user

An Indonesian learner who wants to become more capable in data analysis and needs a clear learning sequence.

Typical characteristics:

- beginner to early-intermediate;
- may know one tool but have gaps in the broader analysis workflow;
- prefers hands-on learning;
- may be learning independently alongside work or study;
- wants practical skills rather than an academic curriculum alone.

### Secondary user

An experienced learner who wants to fill specific skill gaps.

This user should be able to jump directly to SQL, Statistics, Tableau, or another module without being forced through previous modules.

---

## 5. Learning Architecture

BelajarData uses **one canonical curriculum graph**.

Different product surfaces reuse the same underlying learning content rather than creating duplicate courses.

### Main surfaces

#### Learn

The primary guided Data Analyst learning path.

#### Explore Skills

Alternative entry points into canonical skill content, such as SQL, Spreadsheet, Python/Pandas, or Tableau.

Explore Skills must not duplicate lessons that already exist in the main curriculum.

Future optional skill tracks may include Power BI or AI-assisted analyst workflows, but they are outside V1 core scope.

#### Projects

Applied analytical cases where learners combine competencies from multiple modules.

---

## 6. Data Analyst Learning Path

The V1 launch includes the full core curriculum.

### Phase 1 — Foundations

1. Thinking with Data
2. Spreadsheet for Analysis
3. SQL for Data Analysis
4. Python & Pandas for Analysis

### Phase 2 — Working with Data

5. Data Cleaning
6. Exploratory Data Analysis
7. Statistics for Analysts

### Phase 3 — From Analysis to Decision

8. Data Visualization
9. Metrics & Dashboards
10. Tableau for Data Analysis

### Phase 4 — Business Impact

11. Business Analysis
12. Communicating Insights

### Phase 5 — Apply Your Skills

13. Projects

The detailed topic structure, learning objectives, audit decisions, and pedagogical boundaries are maintained in `curriculum.md`.

The application must not duplicate detailed curriculum definitions inside code when the same information can be represented as content/configuration.

---

## 7. Path Access Model

The learning path is **recommended, not locked**.

Users may:

- start from Module 01;
- jump directly to another module;
- open any accessible lesson;
- revisit completed lessons;
- use Explore Skills as an alternate entry point.

The application may display soft prerequisite guidance such as:

> Recommended knowledge: Python & Pandas. You can still start this module.

Prerequisites must not become hard access restrictions in V1.

### Principle

> Progress represents what the learner has completed, not permission to access later material.

---

## 8. Authentication Model

Core learning must be usable without login.

### Guest users

Guests can:

- browse the curriculum;
- open lessons;
- use core practice experiences where technically feasible;
- progress through modules;
- store lightweight progress locally in the browser.

### Logged-in users

Authentication enables persistence rather than access to the curriculum.

Logged-in users can additionally:

- sync progress;
- save bookmarks;
- preserve learning history;
- preserve project progress;
- continue across devices.

### UX message

> **Belajar tanpa login. Login untuk menyimpan perjalanan belajar.**

If practical, guest progress should be offered for migration to the user's account after authentication.

---

## 9. Core Learning Experience

The recommended topic experience is:

1. **Why**
2. **Learn**
3. **Example**
4. **Practice**
5. **Think**
6. **Further Reading**
7. **Next**

Not every topic must mechanically display all seven blocks. The structure is a pedagogical guide rather than a rigid page template.

### Lesson length targets

- Concept lesson: approximately 5–10 minutes
- Lesson + practice: approximately 10–20 minutes
- Module challenge: approximately 15–30 minutes
- Project: approximately 3–6 hours

Lessons should remain concise and avoid becoming ebook-sized chapters.

---

## 10. Practice Architecture

BelajarData supports three levels of practice.

### 10.1 Lesson Practice

Short embedded exercises, generally 1–3 minutes.

Examples:

- choose the correct metric;
- predict SQL output;
- edit a formula;
- interpret a chart;
- identify a misleading conclusion.

### 10.2 Module Challenge

A contextual case lasting roughly 15–30 minutes.

Challenges integrate multiple topics from the module.

### 10.3 Projects

Substantial, less-prescriptive analytical cases lasting several hours.

Projects test synthesis rather than introduce a new hidden curriculum.

---

## 11. Assessment Model

V1 should maximize deterministic assessment and avoid expensive grading systems.

### Auto-checkable

Examples:

- multiple choice;
- multi-select;
- numeric answers;
- SQL result/output;
- bounded spreadsheet results;
- categorical analytical checkpoints.

### Open-ended

For findings, interpretations, recommendations, and written communication:

1. learner submits or records an answer;
2. BelajarData shows a reference approach;
3. learner reviews a checklist;
4. learner can mark the activity complete.

### Explicit V1 exclusions

Do not implement:

- AI grading;
- admin/manual grading;
- arbitrary notebook parsing;
- Tableau workbook parsing;
- automatic capstone grading.

---

## 12. Practice Environments

### 12.1 SQL Playground — Must Have

The SQL Playground is a core V1 learning environment.

Minimum experience:

- predefined dataset;
- SQL editor;
- Run action;
- result table;
- challenge instructions;
- expected-result validation;
- reset query;
- useful error feedback.

It should support the SQL concepts required by the curriculum without becoming a database-administration environment.

Browser-side execution is preferred where feasible.

### 12.2 Spreadsheet Playground — Bounded

The Spreadsheet Playground supports spreadsheet-analysis lessons.

It may include:

- grid;
- selected predefined datasets;
- formula input;
- sorting/filtering;
- basic calculations;
- expected-result validation.

It must **not** become an Excel clone.

Only features required by the curriculum should be implemented.

### 12.3 Python & Pandas Practice — Prototype Required

Desired experience:

- browser-based code editor;
- Run;
- textual/table output;
- predefined datasets;
- edit → run → error → fix → rerun loop.

Pyodide or another browser-side runtime may be evaluated.

Before making the curriculum dependent on this runtime, prototype:

- initial package loading time;
- Pandas compatibility;
- mobile/low-memory behavior;
- dataframe rendering;
- error handling.

If browser execution proves too heavy, V1 may use a more constrained practice experience plus downloadable notebooks. BelajarData must not become a Colab/Jupyter clone.

### 12.4 Visualization Playground — Bounded

A reusable browser-side visualization exercise environment.

Possible controls:

- predefined dataset;
- selected fields;
- limited chart choices;
- sorting;
- highlighting;
- simple presentation options.

No arbitrary uploads, calculated-field engine, relationship modeling, or full dashboard authoring.

---

## 13. High-Value V1 Interactives

Custom interactive experiences are intentionally limited.

### Required shortlist

1. **JOIN Row Multiplication**
2. **Sampling & Uncertainty**
3. **Visualization Playground**
4. **Metric Tree Builder**
5. **Communication Builder**

These interactions should only be built when manipulation materially improves understanding.

### Deferred candidates

Examples include:

- Granularity Interactive;
- Cleaning Decision Simulator;
- EDA Drill-down Investigation;
- Mean vs Median Interactive;
- Correlation Explorer.

These may initially be implemented as normal exercises.

### Rule

> If manipulating a variable and seeing the consequence helps build intuition, make it interactive. Otherwise, don't.

---

## 14. Tableau Learning

Module 10 uses **Tableau only** in the V1 core path.

BelajarData does not build a Tableau simulator.

Recommended flow:

1. read lesson;
2. download a safe fictional dataset;
3. open Tableau;
4. perform a guided task;
5. return to BelajarData;
6. answer checkpoints;
7. perform guided self-assessment;
8. optionally publish.

Tableau Public publishing is optional.

Lessons must clearly warn learners:

> Do not publish confidential, private, proprietary, or company data to Tableau Public.

NusaMart and other fictional learning datasets are suitable for public exercises.

Advanced Tableau topics such as deep LOD expressions and advanced table calculations are Further Learning rather than V1 core requirements.

---

## 15. Shared Learning World — NusaMart

NusaMart is a fictional business used to create continuity across the curriculum.

It may appear in:

- Spreadsheet;
- SQL;
- Python/Pandas;
- Cleaning;
- EDA;
- Visualization;
- Metrics;
- Tableau;
- Business Analysis;
- Communication;
- Projects.

However, NusaMart must not be used for every small example.

Micro-examples should use varied contexts such as:

- SaaS;
- operations;
- transportation;
- education;
- finance;
- restaurants;
- customer service.

### Principle

> One shared world for continuity, multiple small contexts for transfer.

---

## 16. Further Reading

Further Reading is contextual inside lessons/topics.

Guidelines:

- generally 2–4 resources maximum;
- fewer is acceptable;
- prioritize official documentation and high-quality learning resources;
- explain why each resource is useful;
- optionally show type, level, and approximate time;
- avoid generic link dumps.

BelajarData should curate the wider learning ecosystem rather than attempt to reproduce it.

---

## 17. Projects

The curriculum defines three substantial projects.

### Project 01 — NusaMart Revenue Slowdown

**Focus:** Performance diagnosis  
**Difficulty:** Guided

### Project 02 — Customer Retention Analysis

**Focus:** Customer behavior over time  
**Difficulty:** Intermediate

The project remains analytical rather than introducing churn prediction, survival analysis, clustering, or CLV modeling.

### Project 03 — Delivery Performance Investigation

**Focus:** Operational investigation  
**Difficulty:** Intermediate+

V1 launch requires **at least two polished projects**. The third project may ship shortly after launch if necessary.

### Project structure

Each project uses six stages:

1. The Brief
2. Understand the Data
3. Plan Your Analysis
4. Investigate
5. Validate
6. Communicate

Then:

**Reference Approach**

The reference is one defensible analytical approach, not the only correct solution.

Learners should be encouraged to attempt the project before opening it, but the reference must not be permanently hard-locked.

### Tools

Projects specify required analytical outcomes rather than mandatory software.

Examples of valid workflows may include:

- SQL + Tableau;
- Python + Tableau;
- SQL + Python;
- Spreadsheet + Tableau;
- other reasonable combinations of learned tools.

Not every project must end in a dashboard.

---

## 18. Progress Model

### Core learning progress

Track:

- topic completion;
- module completion;
- phase progress;
- overall Modules 01–12 progress.

### Project progress

Track independently:

- project started;
- stage completion;
- project completed;
- total projects completed.

Example:

**Core Learning:** Completed  
**Projects:** 1 / 3

Completing projects is not required to unlock or access the rest of the learning path.

V1 does not require “complete two projects” to declare the core Modules 01–12 completed.

---

## 19. Bookmarks and Learning History

Logged-in learners should be able to bookmark useful learning content.

Minimum bookmark targets:

- topic/lesson;
- optionally Further Reading resource.

Learning history should enable the learner to easily resume recent work.

The system does not need a complex activity feed.

---

## 20. Navigation and Information Architecture

Recommended primary navigation:

- Learn
- Explore Skills
- Projects

Secondary account actions may include:

- Progress
- Bookmarks
- Profile / Account

Avoid exposing unnecessary product complexity in the primary navigation.

### Learn page

Should communicate:

- the five learning phases;
- modules inside each phase;
- module purpose;
- progress;
- recommended sequence;
- freedom to jump ahead.

### Module page

Should communicate:

- module goal;
- topics;
- challenge;
- expected learning outcome;
- estimated effort;
- progress.

### Lesson page

Should prioritize the learning content and practice experience over site chrome.

---

## 21. Content Management Model

Learning content should be maintainable without hardcoding entire lessons into controllers or templates.

At minimum, the content model should represent:

- phase;
- module;
- topic;
- lesson/content blocks;
- practice;
- challenge;
- Further Reading;
- prerequisites/recommended knowledge;
- datasets;
- estimated time;
- ordering;
- publication status.

Content may initially be seeded from structured files/database seeds if a full admin CMS would slow V1 delivery.

A sophisticated authoring CMS is **not required for V1**.

---

## 22. Suggested Domain Model

Exact schema should be finalized during architecture design.

Potential entities:

- `users`
- `phases`
- `modules`
- `topics`
- `content_blocks`
- `practices`
- `practice_attempts`
- `challenges`
- `challenge_attempts`
- `datasets`
- `further_readings`
- `user_topic_progress`
- `user_module_progress` or derived module progress
- `bookmarks`
- `projects`
- `project_stages`
- `user_project_progress`

Avoid storing redundant progress if it can be reliably derived.

Interactive-specific state should remain minimal and only be persisted when it improves learner continuity.

---

## 23. Technical Direction

### Application

- Laravel
- MySQL
- server-rendered application or lightweight frontend enhancement
- browser-side JavaScript for interactive learning experiences

### Laravel responsibilities

- authentication;
- curriculum/content delivery;
- user progress;
- bookmarks;
- learning history;
- project state;
- challenge/assessment records;
- dataset metadata;
- application routing and authorization.

### Browser responsibilities

Where appropriate:

- SQL execution;
- spreadsheet interaction;
- visualization interaction;
- statistical simulations;
- Python runtime if prototype is successful.

### Static/downloadable assets

- CSV;
- XLSX;
- sample datasets;
- images;
- optional notebooks.

### Architectural principle

> Laravel manages the learning product. Browser runtimes and professional tools execute the specialized learning experiences.

---

## 24. Responsive Experience

The main curriculum, reading, progress, bookmarks, and simple assessments should work on mobile.

However, complex hands-on environments may communicate that desktop is recommended.

Do not compromise the desktop learning experience by forcing spreadsheet, SQL, Python, or Tableau workflows into an unusable mobile interface.

---

## 25. Accessibility and Usability

V1 should aim for:

- readable typography;
- sufficient contrast;
- keyboard-accessible core navigation;
- clear focus states;
- labels not dependent only on color;
- understandable validation/error messages;
- code blocks that remain readable;
- responsive lesson content.

Interactive components should include non-interactive explanations where practical so the concept is not hidden behind interaction alone.

---

## 26. Visual/Product Design Direction

BelajarData should feel like a focused learning product rather than an AI-generated dashboard template.

Desired qualities:

- clean;
- calm;
- content-first;
- generous whitespace;
- strong typography;
- restrained card usage;
- clear information hierarchy;
- minimal decorative gradients/effects;
- consistent learning states.

Detailed UI rules belong in `design.md`, not this PRD.

---

## 27. Search

Global search is useful but **not a launch blocker**.

If included in V1, it may search:

- modules;
- topics;
- lesson titles;
- skills.

Full-text semantic search is not required.

---

## 28. Analytics

Product analytics should answer questions such as:

- where learners start;
- module/topic completion rates;
- lesson drop-off;
- practice attempt/completion;
- challenge completion;
- guest-to-account conversion;
- project starts/completions;
- most-used skill entry points.

Do not instrument analytics in a way that blocks core learning.

---

## 29. V1 Functional Requirements

### Learning

- Full Modules 01–12 available at launch.
- Five phases displayed.
- Module 13 Projects area available.
- Topics ordered according to curriculum.
- Soft prerequisites supported.
- Lessons accessible without hard prerequisite locks.
- Module challenges supported.
- Further Reading supported.

### Practice

- SQL Playground.
- Bounded Spreadsheet Playground.
- Python/Pandas practice based on prototype outcome.
- Visualization Playground.
- Auto-checkable question types.
- Guided self-assessment.

### Interactive learning

- JOIN Row Multiplication.
- Sampling & Uncertainty.
- Metric Tree Builder.
- Communication Builder.
- Visualization Playground.

### Account

- registration/login;
- persisted progress;
- bookmarks;
- recent/resume state;
- project progress.

### Guest

- core curriculum access;
- local progress where feasible;
- no mandatory registration wall before learning.

### Projects

- at least two polished projects at launch;
- six-stage project structure;
- reference approach;
- independent project progress.

---

## 30. Non-Goals — V1

The following are explicitly outside V1:

- AI tutor;
- AI-generated lesson content at runtime;
- AI grading;
- chatbot;
- certificate;
- leaderboard;
- XP/gamification system;
- community/forum;
- video-course platform;
- Data Scientist path;
- Data Engineer path;
- Machine Learning curriculum;
- Power BI core track;
- Tableau simulator;
- full Excel clone;
- full notebook/Colab clone;
- arbitrary user dataset uploads;
- automated capstone grading;
- placement test;
- skill-proficiency test;
- complex dashboard builder;
- manual instructor grading;
- job board;
- subscription/paywall implementation unless separately scoped.

---

## 31. Content Requirements Before Launch

Because V1 launches the full curriculum, launch readiness requires content completeness, not merely functional pages.

Minimum content readiness:

- Modules 01–12 published;
- every core topic has learning content;
- required lesson practices implemented;
- every module has its intended challenge or explicitly approved equivalent;
- required datasets available;
- Further Reading curated where useful;
- Tableau exercises tested with safe datasets;
- SQL exercises validated;
- at least two projects fully playable end-to-end;
- reference answers/checklists reviewed for analytical correctness.

A module should not be marked complete merely because its page exists.

---

## 32. Quality Bar

Before launch, validate:

### Curriculum

- no accidental dependency on material taught later;
- no unnecessary duplication;
- terminology is consistent;
- metric definitions remain consistent across shared datasets.

### Exercises

- expected answers are correct;
- edge cases are tested;
- reset/retry works;
- instructions are unambiguous.

### Datasets

- fictional/public-safe where required;
- schema documented;
- values internally coherent;
- deliberate data-quality issues are pedagogically justified.

### Analytics and conclusions

Reference analyses must avoid unsupported causal conclusions.

### Tableau

All publishing guidance clearly distinguishes safe fictional/public data from private or company data.

---

## 33. Migration from Existing BelajarData

V1 should be treated as a **greenfield product rebuild** rather than forcing the new architecture into the existing SQL-oriented application.

Reuse selectively:

- validated SQL lesson content;
- useful SQL datasets;
- challenges that fit the new curriculum;
- brand/domain;
- reusable assets where quality is sufficient.

Do not preserve old architecture solely to avoid rewriting it.

The new curriculum is the source of truth.

Migration should be content-selective, not architecture-preserving.

---

## 34. Release Strategy

### V1 launch target

Release the complete Modules 01–12 learning journey.

At launch:

- curriculum is complete;
- core navigation/progress works;
- practice environments required by published lessons are stable;
- highest-value interactives are available;
- at least two substantial projects are complete.

### Post-launch priorities

Candidate improvements:

- third project;
- deferred interactives;
- additional project library;
- Explore Skills expansion;
- Power BI skill track;
- selected advanced statistics;
- optional AI-assisted analyst workflows;
- richer search;
- improved content-authoring workflow.

Priorities should be driven by learner behavior and feedback rather than feature novelty.

---

## 35. Success Metrics

Initial product metrics should remain simple.

Potential V1 measures:

### Activation

- percentage of visitors who start a lesson;
- percentage who complete their first topic;
- percentage who complete their first practice.

### Learning engagement

- topic completion;
- module completion;
- challenge participation/completion;
- return rate;
- resume usage.

### Depth

- learners reaching later phases;
- project starts;
- project completions.

### Account value

- guest learners who choose to create an account;
- logged-in learners returning to continue progress.

Avoid optimizing for superficial metrics such as raw page views at the expense of learning completion.

---

## 36. Open Technical Questions

These questions should be resolved during architecture/prototyping rather than by expanding the curriculum.

1. Which browser-side SQL engine best fits the required SQL dialect and validation model?
2. How small can the Spreadsheet Playground remain while supporting Module 02?
3. Is Pyodide/Pandas performance acceptable for the intended hosting and learner devices?
4. How should guest progress be merged into an account after login?
5. Which content representation best balances authoring speed and maintainability?
6. Which interactive components can share a common exercise shell?
7. Which progress values should be stored versus derived?
8. How should dataset versions be managed so exercises and expected results remain reproducible?

---

## 37. Acceptance Criteria for V1

BelajarData V1 is ready to launch when a new learner can:

1. open BelajarData without creating an account;
2. understand the recommended Data Analyst path and its five phases;
3. start Module 01 or jump to another module;
4. complete lessons and contextual practice;
5. use the required browser-based practice environments;
6. receive deterministic feedback on auto-checkable exercises;
7. use reference/checklist feedback for open-ended work;
8. complete the full learning content through Module 12;
9. perform the Tableau learning workflow using safe learning data;
10. start and complete at least two substantial projects;
11. create an account to preserve progress and bookmarks;
12. return later and continue from their saved state.

The experience should demonstrate a coherent progression from:

> **business question → data → analysis → evidence → decision → communication**

---

## 38. Product Boundary

BelajarData V1 is **not**:

- a generic online-course marketplace;
- a syntax encyclopedia;
- an AI tutor product;
- a professional BI replacement;
- a hosted notebook platform;
- a certification platform.

It is:

> **A guided, practice-oriented learning environment for becoming capable at data analysis.**

---

## 39. Source-of-Truth Rule

`curriculum.md` defines **what and why to teach**.

This PRD defines **what the product must provide to deliver that curriculum**.

Future `design.md` should define **how the experience should look and behave**.

Future architecture/task documents should define **how it is implemented and sequenced**.

Do not move detailed visual design or implementation tasks into this PRD unless they represent a product requirement.
