# Sistem Informasi BP2M — Universitas PMC

Internal web system for **BP2M (Badan Penelitian dan Pengabdian kepada
Masyarakat) Universitas PMC** (Universitas Pekanbaru Medical Center): one
place to record lecturers' research output — Penelitian, Pengabdian kepada
Masyarakat, Publikasi, Buku and HKI, each with its supporting documents —
plus user accounts, master data, dashboard and recap/export. Full
specification:
[`File Skema/BP2M v1.0.0/PRD-BP2M-PMC.md`](File%20Skema/BP2M%20v1.0.0/PRD-BP2M-PMC.md)
— **read the relevant PRD section before building any module**; this file
is orientation, not a replacement for it. PRD §5 is the database schema;
items marked **[ASUMSI]** are unconfirmed (PRD §11) — implement them in one
easy-to-change place (Policy / `config/bp2m.php`), never scattered.

Sprint-by-sprint plan with checklists and current build status:
[`plan/README.md`](plan/README.md).

## Stack

- **Backend:** Laravel 12, PHP 8.4, MySQL 8.0, Pest (SQLite in-memory),
  Laravel Pint. Extra packages: `blade-ui-kit/blade-heroicons` (icons),
  `maatwebsite/excel` (export, Sprint 7).
- **Frontend:** **Blade**, server-rendered — no SPA, no
  Inertia/React/Vue/Livewire. Tailwind CSS **v4** (`@tailwindcss/vite`,
  CSS-first), **Alpine.js 3** (+ `@alpinejs/focus`) for small interactions,
  **Tom Select** for the searchable lecturer picker, Vite. Self-hosted font
  `@fontsource-variable/inter`. No UI kit (Bootstrap/Flowbite/DaisyUI), no CDN.
- **Auth:** hand-written session login with **username + password** — no
  e-mail column, no self-registration, no Breeze/Jetstream.

## Coding standards (read before writing code)

@rules/backend-standards.md
@rules/database-standards.md
@rules/frontend-standards.md
@rules/design-standards.md
@rules/security-standards.md

## Project-specific skills

- `blade-module` (`skills/blade-module/SKILL.md`) — recipe for a module end
  to end (migration → enum → model → request → service → policy →
  controller → route → views → navigation → tests). **Penelitian (Sprint 3)
  is the reference module**; later data modules copy its shape.
- `blade-component` (`skills/blade-component/SKILL.md`) — recipe for a
  reusable Blade component in `resources/views/components/`.
- `front-end-design` (`skills/front-end-design/SKILL.md`) — the PMC design
  system applied to pages (list, form, detail, dashboard).

## Golden rules (the ones people actually get wrong)

1. **Two roles only: `ADMIN` (staf BP2M) and `DOSEN`** in `users.role`
   (string cast to `App\Enums\UserRole`) — no Spatie, no role table.
   No Pimpinan role for now (PRD §11.1). Pengguna, Data Master, Verifikasi,
   Rekap and Pengaturan sit behind `role:ADMIN`; every data
   record also goes through its Policy (`{Model}Policy extends
   ActivityPolicy`) — a role check alone is never enough.
2. **Ownership lives in one place (PRD §7).** A Dosen *sees* a record when
   they created it or are an internal contributor; *edits/deletes/submits*
   it when they created it or are the lead contributor (`sort_order = 1`)
   **and** its status is `DRAFT` or `NEEDS_REVISION` (rule 12). The rule
   is coded once — `scopeVisibleTo($user)` in the `HasContributors` trait
   for lists, `ActivityPolicy` for single records — and reused by index,
   search, dashboard, recap and export. Never re-write
   `where('created_by', …)` in a controller.
3. **People on a record are `contributors`, never a text column.**
   Peneliti / pengabdi / penulis / pemilik KI share one polymorphic
   `contributors` table (internal lecturer = `user_id`, outside person or
   institution = `name` + `affiliation`), ordered by `sort_order` (1 = ketua
   / penulis pertama), written only through `ContributorService::sync()`.
   A Dosen who creates a record is added automatically and cannot remove
   themself.
4. **Dokumen pendukung are private PDFs.** Polymorphic `documents` table +
   the `local` disk (`storage/app/private/documents/{morph}/{id}/`), random
   stored names, written only through `DocumentService`, served only through
   `documents.download` after the parent's Policy allows `view`. Never the
   `public` disk, `storage:link`, `asset()` or `Storage::url()`. Limits
   (PDF, 10 MB, 10 files) live in `config/bp2m.php`.
5. **Morph map is enforced** (`Relation::enforceMorphMap`): `research`,
   `community_service`, `publication`, `book`, `intellectual_property`.
6. **Masters are never free text.** Fakultas, Prodi, Skema and Sumber Dana
   are tables picked through `<x-form.select :options="…::options()">`.
   `users` stores only `study_program_id`; the faculty is derived through
   the prodi. A scheme has a `type` (`RESEARCH` / `COMMUNITY_SERVICE`) — a
   form offers only its own type. Used masters are deactivated, not deleted.
7. **Users with data are deactivated (`is_active`), never deleted.**
   Inactive accounts cannot log in and are logged out on their next request.
   An Admin cannot deactivate or demote themself.
8. **`Research` model needs `protected $table = 'researches';`** — Laravel's
   pluralizer treats "research" as uncountable.
9. **Tailwind v4, CSS-first; modern white layout in the PMC colours**
   (user, 2026-10-10: modern/white like their reference, but keep the
   institution's colours). Tokens live only in `resources/css/app.css`
   `@theme`: `brand-*` (PMC red, primary actions/active markers,
   `brand-600` = `#B50606`), `gold-*` (accent/logo), `navy-*` (info),
   neutral `primary-*` greys for hovers, `canvas`/`surface`/`line`/`ink`/
   `muted`, pastel status sets `{success,warning,danger,info,accent}` +
   `-soft`/`-line`/`-ink`. Never a hex literal or default Tailwind colour
   (`bg-red-700`) in a view. See `design-standards.md`.
10. **Components before markup.** Buttons, cards, tables, fields, badges,
    flash, empty states come from `resources/views/components/{ui,form,shared,layouts}`;
    create and edit share one `_form.blade.php`.
11. **Navigation tracks reality.** The sidebar is built from
    `App\Support\Navigation`; an item gets its route only once its `index`
    route exists — until then it renders disabled ("Segera"). Admin-only
    items carry `roles: ['ADMIN']`.
12. **Every data record is verified by an Admin (PRD §4.11, §6.4).**
    Status `DRAFT → SUBMITTED → VERIFIED`, with `NEEDS_REVISION` (note
    required) as the way back; cast to `App\Enums\VerificationStatus`.
    The `status` column is **never** filled from a form — it moves only
    through `VerificationService` (`submit`, `verify`, `requestRevision`,
    `reopen`, `markVerifiedByAdmin`), which also writes the append-only
    `verification_logs` row in the same transaction. Records created or
    edited by an Admin become `VERIFIED` directly. Dashboard, Rekap and
    export count **only `VERIFIED`** (`scopeVerified()`) unless a status
    filter says otherwise. The Admin queue is one page (`verifications.index`)
    reading `SUBMITTED` across all five modules.
13. **The logo is uploaded in the app, not committed.** Admin uploads it in
    Pengaturan (`settings.edit`; PNG/JPG/WebP ≤1 MB, **no SVG**); the path
    lives in the `settings` table (`App\Support\Settings::logoUrl()`, cached)
    and is the **only** file on the `public` disk. Without a logo the layout
    shows the placeholder (red "PMC" tile, gold text).

## Git

- Repo: `https://github.com/idosiregar24/bp2m-pmc` (remote `origin`, branch
  `main`). **The user commits and pushes themself** — never run
  `git commit`/`git push`; finish a chunk of work by proposing a commit
  message (Conventional Commits, English subject, e.g.
  `feat(research): add research CRUD with contributors`) and the files to
  stage.
- `.claude/settings.local.json` is git-ignored (per-machine permissions).

## Local environment

- **PHP 8.4** — Laragon at
  `C:\Users\user\Documents\laravel\laragon-6.0-minimal\bin\php\php-8.4.12-nts-Win32-vs17-x64`
  (8.2 is also installed; make sure Laragon/PATH use 8.4). Composer 2.8,
  Node 24, **MySQL 8.0.30** (Laragon).
- Sprints 00–07 are built (all five modules, Rekap, export). Shared module
  code: `ActivityService`, `ActivityPolicy`, `UpdatesActivity` request
  trait, `ActivityExport`, factory trait `ActivityStates`, registry
  `App\Support\Verifiable`. A new data module = one registry entry + its
  own model/request/controller/views/export.
- DB: `bp2m_pmc`, user `root`, no password.
- App: `php artisan serve --port=8020` (8000/8010 are taken by other local
  projects) or the Laragon vhost `http://bp2m-pmc.test` (auto-created from
  the folder name after a Laragon reload).
- `composer dev` = server + queue + `npm run dev`. `npm run build` must pass
  before a commit.
- `php artisan migrate:fresh --seed` = demo data: `admin` / `password`
  (ADMIN), `dosen` / `password` (DOSEN), sample masters and records.
  `DatabaseSeeder` refuses to run when `APP_ENV=production`; production uses
  `ProductionSeeder` (masters + one admin whose password comes from
  `ADMIN_INITIAL_PASSWORD`, must change on first login).
- `php artisan test` (Pest, SQLite in-memory via `phpunit.xml`) — target
  ≥70% coverage. **Never run `config:cache` locally**: a cached config makes
  the tests ignore `phpunit.xml` and hit the MySQL dev database.

## Language

Code, comments, identifiers, table/column/enum values in **English**.
Everything **user-facing** — UI text, URL segments (`/penelitian`,
`/pengabdian`, `/publikasi`, `/buku`, `/hki`, `/pengguna`, `/master`),
validation messages (`APP_LOCALE=id`, `lang/id/*.php`), flash messages,
enum `label()`s, Excel headers — in **Bahasa Indonesia**.
