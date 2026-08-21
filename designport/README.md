# DesignPort

DesignPort is a production-oriented portfolio builder built with Next.js 14, TypeScript, Tailwind CSS, Framer Motion, Auth.js, PostgreSQL and Prisma. Users authenticate with Google or GitHub, compose a responsive page from accessible blocks, autosave it and publish it to a stable public URL.

The complete product/design rationale, five sprint file maps and manual UX checks are in [docs/BUILD_GUIDE.md](docs/BUILD_GUIDE.md).

## Quick start with Docker

1. Copy `.env.example` to `.env` and generate `NEXTAUTH_SECRET` with `openssl rand -base64 32`.
2. Add at least one OAuth provider. Google’s callback URL is `http://localhost:3000/api/auth/callback/google`; GitHub’s is `http://localhost:3000/api/auth/callback/github`.
3. Run `docker compose up --build`.
4. Open `http://localhost:3000`. The health endpoint is `http://localhost:3000/api/health`.

The compose stack waits for PostgreSQL, deploys committed migrations, and starts Next.js in development mode with source mounting.

## Local start without Docker

```bash
pnpm install
cp .env.example .env
pnpm db:deploy
pnpm dev
```

PostgreSQL must already be reachable through `DATABASE_URL`.

## Verification

```bash
pnpm typecheck
pnpm lint
pnpm test
pnpm build
```

The build command packages Next.js static chunks, stylesheets, fonts and `public/` files into the standalone output. Run the optimized local server with:

```bash
PORT=3001 pnpm start
```

## OAuth production setup

- Set `NEXTAUTH_URL` to the canonical HTTPS origin.
- Replace both callback origins in the Google Cloud Console and GitHub OAuth App settings.
- Store client secrets and `NEXTAUTH_SECRET` in the deployment platform’s secret manager, never in source control.
- Use a managed PostgreSQL database, run `pnpm db:deploy` as a release step, then start the production image.

## Production notes

- The production Docker stage runs as the unprivileged `nextjs` user and serves Next’s standalone output.
- API mutations validate bounded payloads with Zod and verify resource ownership server-side.
- JWT sessions are signed by Auth.js. OAuth account records remain in PostgreSQL for durable account linking.
- Add platform-level rate limiting to `/api/auth/*` and mutating `/api/portfolios/*` routes before exposing a high-traffic public deployment.
- Back up PostgreSQL and test restoration. Database availability is reported through `/api/health` without exposing connection details.
