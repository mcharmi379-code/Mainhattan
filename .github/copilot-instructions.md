<!-- Workspace Copilot instructions — keep concise and link-first -->
# Copilot / Agent Instructions

Purpose
-------
Provide a small, actionable guide for assistant interactions and where to find authoritative project docs.

Quick start
-----------
- Ask short, focused requests (one technical change per request).
- Reference files by path (the assistant links them back).

Scope / What the assistant may do
---------------------------------
- Edit, create, or refactor PHP/Symfony code under `src/`, `config/`, and `public/`.
- Run repository-prescribed tasks when asked (build scripts in `bin/`).
- Propose tests and small CI-friendly changes; do not alter project-wide policies without consent.

Where to look (links)
---------------------
- Composer / dependencies: [composer.json](composer.json#L1)
- Configuration and services: [config/services.yaml](config/services.yaml#L1)
- Build and helper scripts: [bin](bin)

How to ask
----------
- Provide the goal, the target file(s) or directory, and any constraints (PHP version, coding style).
- Example: "Update route handling in `src/Controller/PaymentController.php` to validate input and add unit tests."

ApplyTo suggestions
-------------------
- Use `applyTo` patterns for large or area-specific rules (e.g., `src/**` for backend, `bundles/administration/**` for admin UI).

Anti-patterns
-------------
- Don't paste long design docs — link them instead.
- Avoid multi-purpose requests that mix large refactors with unrelated feature work.

Example prompts
---------------
- "Create a small migration that adds column X to table Y and update Doctrine entity." 
- "Fix failing route in `public/index.php` and add a short test verifying behavior." 

Next steps
----------
- Ask the team whether to enforce `applyTo` rules for frontend vs backend.
- Optionally add targeted agent customizations in `/docs/agents/` or `AGENTS.md`.
