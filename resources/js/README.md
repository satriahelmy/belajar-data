# Frontend assets

Vite builds `resources/css/app.css` and `resources/js/app.js` into `public/build`.

JavaScript progressively enhances server-rendered pages. `components/registry.js` is the only application entry point for registered learning blocks; each component is a small lazy-loaded module with a server-rendered fallback.

Heavy SQL, spreadsheet, Python, and charting runtimes are future adapters. They must not be imported by `app.js` and must remain bounded and lazy-loaded when their milestone begins.
