# Product Requirements Document

**Product:** Ibrahim EL-Seyoufi Architecture Portfolio
**Status:** Implementation brief
**Updated:** 2026-11-15

## 1. Product overview

A public architecture portfolio and private content-management area. The application will be served by one Laravel application using Livewire and MySQL. The public pages and admin tools share the existing project database and image storage.

## 2. Problem and goals

The current application separates the public website into Next.js and the admin panel into Filament. This creates two user-facing stacks around one portfolio and API. The refactor brings both experiences into Laravel while preserving current content, image paths, public style, and core page behavior.

Goals:

- Serve the public site and admin from Laravel and Livewire.
- Keep MySQL as the source of truth and retain existing data.
- Keep the existing visual identity, project pages, About page, social links, and contact details; make the dark theme the default and keep all controls easy to find.
- Support main projects containing internal projects.
- Let an administrator upload the logo and homepage image.
- Improve behavior during slow or interrupted internet connections.

## 3. Users

- **Visitor:** Browses the studio's projects, internal project galleries, About information, and contact links.
- **Administrator:** Signs in privately to manage site settings, categories, projects, images, inquiries, and existing analytics.

## 4. Core experiences

### Public website

- A slim, uploaded logo stays centered in a sticky header. The navigation menu sits on the left beside the studio name and contains page links, account access, language, and color mode.
- The home image occupies about two thirds of a mobile viewport, with the portfolio introduction overlaid.
- The home page lists main projects and filters them with a horizontal row of clickable category image tiles. Each category can have its own cover image.
- A main project may contain any number of internal projects. Its detail page uses a full-bleed gallery behind the title and introduction, then presents image-led subproject cards with consistent image ratios. The gallery advances every second, with manual controls and a pause option.
- A project without internal projects continues to show its own image gallery, which also advances every second.
- Existing project, category, biography, social, contact, language, and theme content remains available.

### Administrator

- Administrator sign-in is available at /admin/login. Public account sign-in, sign-up, email verification, password recovery, and password change are available from the public navigation.
- Public sign-up creates non-admin users. Only a trusted administrator command can grant admin access.
- Administrators can create, edit, archive, restore, and permanently delete projects.
- Project forms can attach a project to a main project, upload a description and gallery, and select a cover image. A saved subproject prompts the administrator to add another under the same parent. Subprojects inherit the parent category.
- Administrators can manage category names, images, and visibility, and update site copy, logo, home image, profile photo, social links, contact details, and search metadata.
- Existing inquiries and analytics remain readable from the admin area.

## 5. Functional requirements

| ID | Requirement | Acceptance criteria |
|---|---|---|
| FR-1 | Replace the Next.js public frontend and Filament admin with Laravel and Livewire. | Public and admin routes are rendered by the Laravel application; tracked Next.js and Filament application code and dependencies are removed. |
| FR-2 | Preserve the current database and its records. | Migrations add nullable project-parent, logo, and category-image fields, plus an admin flag; existing project, category, image, settings, inquiry, analytics, and user rows remain. No production wipe or reset is used. |
| FR-3 | Support internal projects. | A project can be assigned to a top-level project; visitors can see a full-bleed internal-project gallery, then image-led subproject cards with descriptions and direct detail pages. Admins can add subprojects repeatedly without leaving the parent selection, and child categories inherit from the parent. |
| FR-4 | Rotate internal-project images automatically. | The detail-page gallery advances every second when motion is allowed, pauses on request and while focused or hovered, and has manual controls. |
| FR-5 | Provide homepage brand controls. | The admin can upload a logo and home image; the logo is slim and centered in a sticky header, and the mobile home image uses roughly two thirds of the viewport height. |
| FR-6 | Preserve content-management workflows. | Admins can manage projects, archived projects, images, categories, site settings, inquiries, and analytics without Filament. |
| FR-7 | Secure admin operations. | Login is rate-limited; only admin accounts can open admin pages or call their Livewire actions; existing administrator access is retained during migration. |
| FR-8 | Handle images safely. | Uploads are validated, resized, converted to WebP, stored on the public disk, and never accept executable files. |
| FR-9 | Handle network interruption gracefully. | Public assets and a bounded number of previously opened public pages can be served from cache; admin pages, Livewire requests, uploads, and form submissions are never cached. Offline visitors receive a clear message and manual refresh action. |
| FR-10 | Maintain public metadata and navigability. | Home, Projects, About, and project detail routes use stable named URLs, page titles, descriptions, and canonical URLs. |
| FR-11 | Provide complete account access flows. | Users can register, verify email, sign in, sign out, request a reset link, reset a forgotten password, and change a password after confirming the current one. |
| FR-12 | Enforce authentication and authorization on the server. | Sessions use CSRF protection, rotate after authentication, and are invalidated on logout; registration cannot create admins; every admin route and Livewire action checks `is_admin`; login, registration, verification, and reset requests are rate-limited. |

## 6. Production and quality requirements

- MySQL connection settings remain in the private environment file; migrations are additive and production-safe.
- Public content uses a short-lived cache with a prior-value fallback where the independent cache store is available.
- Production uses HTTPS, secure session cookies, rate limits, and security response headers.
- The Laravel public directory is the only web document root. Environment files, logs, uploaded originals, source code, and vendor dependencies remain private.
- Uploaded photos are stored as optimized WebP. Cache size is bounded.
- Galleries include meaningful alt text, controls, and reduced-motion behavior.
- Offline behavior is read-only; the interface must not claim that an offline admin change or upload was saved.

## 7. Data and migration plan

- Keep existing projects, project images, categories, site settings, users, inquiries, and page views tables and their data.
- Add a nullable self-referencing projects.parent_id so old projects remain top-level by default.
- Add nullable site_settings.logo_path.
- Add users.is_admin as false by default. Promote selected administrators explicitly through the trusted CLI; new public registrations remain non-admin.
- Do not run migrate:fresh, db:wipe, or destructive production rollbacks.

## 8. Out of scope

- Replacing MySQL or importing content into a new database.
- Editing portfolio content while offline.
- Introducing a new visual identity or copying unrelated storefront functionality from Athar.

## 9. Success criteria

- The public and admin sites run from Laravel and Livewire without a Next.js runtime or Filament.
- Existing MySQL content and uploaded images remain usable.
- Main projects and their internal projects can be managed and navigated.
- The home logo and hero image can be changed from admin.
- Slow or lost connections do not expose admin content or falsely report unsaved work as saved.
