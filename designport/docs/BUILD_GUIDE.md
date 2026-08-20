# DesignPort: five-sprint build guide

This guide explains the design and engineering decisions behind the complete source in this repository. Every path listed below contains its full implementation—there are no pseudocode or omitted sections.

## Product vision and design system

DesignPort should feel like a quiet creative director: assured, specific and never louder than the user’s work. Its core loop is **compose → refine → publish**. The interface supports that loop with progressive disclosure and immediate, reversible feedback.

### Typography

Cormorant Garamond is used only for display copy, where its calligraphic contrast gives the product an editorial, luxury character. Manrope carries UI labels, form fields and body copy because its open forms remain legible at small sizes. The 1.25 modular scale is 12, 14, 16, 20, 25, 31, 39, 49 and 61 px. Body text uses a relaxed 1.625 line height and readable content widths.

### Color

- Warm ivory (`#f7f4ee`) conveys craft and is gentler than clinical white.
- Obsidian (`#1b1815`) conveys authority and creates strong accessible contrast.
- Champagne (`#c9aa67`) signals refinement but is an accent, never the only state cue or small text on ivory.
- Amethyst (`#7c5ce5`) marks interactivity and focus without imitating generic enterprise blue.
- Dark surfaces retain warm undertones so theme changes preserve brand character.

All semantic pairs target WCAG 2.1 AA: 4.5:1 for normal text and 3:1 for large text and meaningful UI boundaries. Published/draft and saved/error states use text and icons as well as color.

### Spacing, shape and motion

Spacing follows a 4 px base grid. Interactive targets are at least 44×44 px. Controls, cards and large page surfaces use 12, 16 and 24 px radii respectively, establishing hierarchy without decorative noise. Motion uses 160 ms for hover feedback, 240 ms for component changes and 400 ms for entrances. Framer Motion animates only opacity and transforms. `prefers-reduced-motion` reduces both Framer and CSS motion.

### Mobile-first breakpoints

| Range | Reason |
| --- | --- |
| Base, 320–639 px | One-column reading and task flow; works on narrow phones without horizontal scrolling. |
| `sm`, 640 px | Enough width for paired actions and form fields without shrinking 44 px controls. |
| `md`, 768 px | Typical portrait tablet width; feature and portfolio cards can form useful columns. |
| `lg`, 1024 px | Minimum viable width for palette, canvas and inspector to coexist as three usable regions. |
| `xl`, 1280 px | Expands the central canvas toward a realistic landing-page preview. |
| `2xl`, 1536 px | Caps application chrome so line lengths and pointer travel do not become excessive. |

### Accessibility contract

- Semantic landmarks, heading order, labels and descriptive link/button names.
- A skip link and focus transfer to `#main-content` after navigation.
- Persistent two-pixel focus rings with sufficient contrast in both themes.
- DnD Keyboard Sensor support plus explicit Add, Move up/down, Duplicate and Delete actions.
- Polite live announcements for builder changes and autosave; assertive roles for errors.
- No color-only state, no hover-only required control, and no animation requirement.
- Public content is rendered as text rather than injected HTML; links are protocol allow-listed.

## Sprint 1 — Foundation and brand system

**Goal:** produce the responsive shell, luxury theme, persistent color preference, motion primitives and recoverable application states.

### Design rationale before components

- `Navbar` is intentionally sparse because stable orientation and one primary action reduce choice cost.
- `ThemeToggle` is a 44 px icon control with a computed label because icon-only meaning must remain available to assistive technology.
- `PageTransition` moves only eight pixels and focuses the new main landmark; this provides continuity without delaying navigation.
- `Skeleton` mirrors final card geometry to reduce perceived wait and cumulative layout shift.
- Error boundaries center one explanation and one recovery action because an error state should not compete for attention.
- Landing feature cards use Gestalt proximity: each icon, title and explanation form one scan unit.

### Exact files added

```text
package.json
pnpm-lock.yaml
tsconfig.json
next-env.d.ts
next.config.mjs
postcss.config.js
tailwind.config.js
.eslintrc.json
.gitignore
app/
├── globals.css
├── icon.svg
├── layout.tsx
├── page.tsx
├── loading.tsx
├── error.tsx
├── global-error.tsx
└── not-found.tsx
components/
├── landing/
│   ├── feature-grid.tsx
│   └── hero.tsx
└── ui/
    ├── button.tsx
    ├── button-variants.ts
    ├── logo.tsx
    ├── nav-visibility.tsx
    ├── navbar.tsx
    ├── page-transition.tsx
    ├── providers.tsx
    ├── skeleton.tsx
    ├── theme-provider.tsx
    └── theme-toggle.tsx
lib/utils.ts
```

### Build sequence

1. Install the pinned packages in `package.json`.
2. Establish CSS variables for semantic surfaces and foregrounds in `globals.css`.
3. Extend Tailwind with named brand colors, type scale, radii, shadows and transition durations.
4. Mount the theme before React hydration, then persist user changes in local storage.
5. Add motion, loading, local error, global error and 404 states.
6. Assemble the mobile-first marketing route.

### UX checklist

- [ ] At 320 px, no content or focus ring is clipped and CTA buttons remain at least 44 px high.
- [ ] Tab begins with the skip link, then follows the visible navigation and page order.
- [ ] The theme button announces the theme it will switch to and survives reload/navigation.
- [ ] System dark preference is honored when no stored preference exists.
- [ ] Hover and tap states feel responsive; reduced-motion removes page/card travel.
- [ ] Landing headings do not orphan awkwardly at 320, 640, 768, 1024 and 1536 px.
- [ ] Loading UI exposes a spoken loading label and its skeletons do not receive focus.
- [ ] Local and global errors each provide a keyboard-operable recovery control.

## Sprint 2 — Identity, data and dashboard

**Goal:** add PostgreSQL persistence, Prisma, Google/GitHub OAuth, signed JWT sessions, protected pages and portfolio management.

### Design rationale before components

- The sign-in card groups providers because they fulfill one intent and should be perceived as alternatives.
- Provider buttons retain their names during loading so users know which external flow is active.
- Dashboard cards favor recognition over recall by showing title, description, state and relative update time together.
- Publication state is a text badge; its color only reinforces the word.
- Empty state copy gives one next action and previews what will happen, reducing blank-canvas anxiety.
- Destructive deletion is visually secondary, explicitly named for screen readers and confirmed before request.

### Exact files added

```text
.env.example
middleware.ts
prisma/
├── schema.prisma
└── migrations/
    ├── migration_lock.toml
    └── 20260820000000_init/migration.sql
types/next-auth.d.ts
lib/
├── auth.ts
├── http.ts
└── prisma.ts
app/
├── auth/signin/page.tsx
├── dashboard/page.tsx
└── api/
    ├── auth/[...nextauth]/route.ts
    └── health/route.ts
components/
├── dashboard/
│   ├── create-portfolio-button.tsx
│   └── portfolio-card.tsx
└── ui/
    ├── oauth-buttons.tsx
    └── sign-out-button.tsx
```

### Build sequence

1. Model Auth.js users/accounts plus owner-indexed JSON portfolio documents in Prisma.
2. Commit an initial PostgreSQL migration rather than using schema push in deployed environments.
3. Configure Google and GitHub providers, the Prisma adapter and 30-day signed JWT sessions.
4. Attach the stable database user id to JWT and session callbacks.
5. Protect `/dashboard` and `/builder/*` in middleware, then repeat authorization in every data operation.
6. Render the dashboard server-side and isolate request-driven controls in client components.

### UX checklist

- [ ] Unauthenticated dashboard/builder requests redirect to the sign-in route.
- [ ] Google and GitHub buttons enter a disabled loading state and errors are announced.
- [ ] OAuth returns to `/dashboard`; reload preserves the session; Sign out returns home.
- [ ] A first-time user sees the empty state and can create a starter portfolio.
- [ ] Cards show Draft/Published in text and accurate update recency.
- [ ] Delete asks for confirmation, cancel preserves the card, and server errors leave it retryable.
- [ ] Browser back/forward and session expiry never reveal another user’s portfolio.

## Sprint 3 — Accessible direct-manipulation builder

**Goal:** support adding, dragging, reordering, editing, duplicating and undoing portfolio blocks across pointer and keyboard input.

### Design rationale before components

- The library/canvas/inspector model mirrors physical layout work: choose material, arrange it, refine it.
- Palette cards explain block outcomes to help users choose by purpose rather than component jargon.
- A bordered page canvas makes the editable object explicit and separates app chrome from authored output.
- Drag handles avoid accidental movement while selecting or editing; explicit movement controls guarantee an equivalent path.
- The inspector reveals fields for only the current block, applying progressive disclosure to reduce cognitive load.
- Undo/redo live in the persistent toolbar because safe experimentation depends on visible reversibility.

### Exact files added

```text
types/portfolio.ts
lib/
├── blocks.ts
└── validation.ts
app/builder/[id]/page.tsx
components/builder/
├── block-palette.tsx
├── block-renderer.tsx
├── canvas.tsx
├── editor.tsx
├── inspector.tsx
├── portfolio-renderer.tsx
└── sortable-block.tsx
```

### Build sequence

1. Define a discriminated, serializable block document and Zod boundary schemas.
2. Create valid starter templates for Hero, Story, Projects and Contact.
3. Add pointer and keyboard DnD sensors with an activation distance to avoid accidental drags.
4. Implement add/reorder plus explicit move, duplicate and delete alternatives.
5. Keep a bounded 50-state local history for canvas undo/redo.
6. Bind the selected block to a type-specific, labeled inspector and live rendered preview.

### UX checklist

- [ ] Clicking/pressing Enter on a library item appends exactly one block.
- [ ] Dragging the separate handle onto a block inserts at the intended position.
- [ ] Space/arrow/Space keyboard DnD works and changes are announced.
- [ ] Move up/down has correct disabled boundary behavior.
- [ ] Duplicate selects the copy; Delete selects a sensible neighbor; Undo restores deletion.
- [ ] Every inspector control has a label, character limit and visible focus treatment.
- [ ] Project removal cannot leave an invalid zero-project block and maximum is 12.
- [ ] The mobile layout stays in library → settings/canvas → inspector task order.
- [ ] At 1024 px, all three regions are usable without horizontal page scroll.

## Sprint 4 — Autosave, publishing and public pages

**Goal:** persist validated changes, surface save state, manage publishing and render discoverable public portfolios.

### Design rationale before components

- Autosave reduces interruption but reports Ready/Unsaved/Saving/Saved/Error so the system never appears magical or ambiguous.
- A 900 ms debounce balances perceived immediacy with server load during typing.
- Publish remains explicit and saves first; users retain control over when work becomes public.
- Public pages omit product navigation so authored work owns the visual hierarchy.
- The public renderer shares block rendering code with the builder to preserve preview fidelity.

### Exact files added

```text
app/
├── p/[slug]/page.tsx
└── api/portfolios/
    ├── route.ts
    ├── [id]/route.ts
    └── [id]/publish/route.ts
```

### Build sequence

1. Add authenticated create/list/get/update/delete endpoints with try/catch handling.
2. Parse unknown JSON and validate bounded payloads before Prisma calls.
3. Query ownership before update/delete/publish and return generic server failures.
4. Debounce editor changes, abort obsolete requests and guard against stale responses clearing newer dirty state.
5. Save before publishing, toggle a timestamp and reveal the public preview only when published.
6. Query only published slugs, validate stored blocks, create canonical/OG metadata and render semantic output.

### UX checklist

- [ ] Typing shows Unsaved, then Saving, then Saved without moving surrounding controls.
- [ ] Rapid typing produces one final save and stale responses do not overwrite status.
- [ ] Invalid slug/email/link yields a readable error; correcting it recovers autosave.
- [ ] Closing a genuinely dirty tab prompts; a saved tab closes normally.
- [ ] Publish waits for a successful save and public URL opens in a new tab.
- [ ] Unpublish immediately makes the public route return the branded 404.
- [ ] Public pages have one primary heading, semantic sections/projects, safe links and keyboard focus.
- [ ] Public metadata title/description/canonical values match portfolio settings.

## Sprint 5 — Production hardening and delivery

**Goal:** package repeatable local/production environments, verify critical domain boundaries and document the release path.

### Design rationale before components

- The health route is deliberately terse because operational interfaces should be reliable and reveal no infrastructure details.
- Security headers disable framing, MIME guessing and unused sensitive browser capabilities.
- A non-root production runtime minimizes damage if application code is compromised.
- Domain tests focus first on templates and validation because invalid documents are the most expensive failure to debug in the visual editor.

### Exact files added

```text
Dockerfile
docker-compose.yml
.dockerignore
vitest.config.ts
tests/
├── setup.ts
├── blocks.test.ts
└── validation.test.ts
public/.gitkeep
README.md
docs/BUILD_GUIDE.md
```

### Build sequence

1. Add Vitest/jsdom setup and cover each block template plus route-sensitive validation.
2. Enable standalone Next output and baseline security headers.
3. Build development and multi-stage production Docker targets.
4. Compose Postgres and app services with persistent isolated volumes and readiness checks.
5. Run migrations as a deployment/release concern and start production as an unprivileged user.
6. Verify typecheck, lint, unit tests, production build, health endpoint and Docker configuration.

### UX/operations checklist

- [ ] `pnpm typecheck`, `pnpm lint`, `pnpm test` and `pnpm build` all exit zero.
- [ ] `docker compose config` resolves without errors and `docker compose up --build` becomes healthy.
- [ ] `/api/health` returns 200 with reachable DB and 503 without it; neither response exposes credentials.
- [ ] Response headers include no framework signature and include nosniff, frame denial, referrer and permission policies.
- [ ] Production OAuth callbacks use the canonical HTTPS origin and secrets are injected outside the image.
- [ ] `prisma migrate deploy` runs before the new application revision accepts traffic.
- [ ] Database backup restoration, account deletion and portfolio deletion are exercised in staging.
- [ ] Lighthouse Accessibility is 100 and critical routes pass manual screen-reader testing in VoiceOver or NVDA.

## Final production gate

Automated tools cannot prove “100% accessible.” WCAG conformance requires manual keyboard, zoom/reflow, contrast, screen-reader and cognitive review. Before launch, test at 200% and 400% zoom; exercise VoiceOver/Safari and NVDA/Firefox; run axe/Lighthouse on home, sign-in, dashboard, builder and public routes; and repeat those checks for both themes, loading, empty, validation and server-error states.
