# Golden Tap POS

Golden Tap POS is a responsive, security-hardened point-of-sale application for sales, inventory, customers, expenses, receipts, reporting, and team administration. The repository is self-contained PHP and browser assets; it does not ship sample transactions, personal data, or default passwords.

## What is included

- Fast product-catalog checkout with category filters, search, stock limits, customer selection, VAT, and cash-change calculation.
- Server-authoritative pricing, VAT, totals, seller identity, and inventory changes. Checkout, edits, and reversals run in database transactions.
- A print-ready 80 mm receipt after every successful sale, plus receipt reprints and XML invoice exports from Sales history.
- Actual transaction snapshots on receipts: item price, quantity, line total, subtotal, VAT rate and value, total, payment/reference, cash tendered, and change due.
- Administrator dashboard with live daily KPIs, recent sales, quick actions, low-stock alerts, team controls, and an activity audit log.
- Product/category management, customer records, expense tracking, date-range reports, CSV exports, and sales performance charts.
- Role-based access for administrators, sellers, and inventory specialists.

## Roles

| Capability | Administrator | Seller | Special |
| --- | :---: | :---: | :---: |
| Dashboard and audit log | Yes | — | Limited dashboard |
| Checkout and receipt printing | Yes | Yes | — |
| Sales and customer history | Yes | Yes | — |
| Edit/reverse completed sales | Yes | — | — |
| Products and categories | Yes | — | Yes |
| Expenses and reports | Yes | Yes | — |
| Team accounts and roles | Yes | — | — |

## Requirements

- PHP 8.2 or newer
- PHP extensions: `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, and `simplexml`
- MySQL 8.0+ or MariaDB 10.6+
- Apache 2.4 with `mod_rewrite` and `AllowOverride All`
- HTTPS in production

The app has no Composer or Node build step. Required browser libraries are stored locally under `views/`.

## New installation

1. Create an empty database and a restricted application user. Replace the example password before running these commands:

   ```sql
   CREATE DATABASE posystem CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'pos_app'@'localhost' IDENTIFIED BY 'replace-with-a-long-random-password';
   GRANT SELECT, INSERT, UPDATE, DELETE ON posystem.* TO 'pos_app'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. Load the clean schema with a database administrator account:

   ```bash
   mysql -u root -p posystem < database/schema.sql
   ```

3. Create the local configuration:

   ```bash
   cp .env.example .env
   chmod 600 .env
   ```

   Set the database password and verify the business identity, currency, VAT label, timezone, and optional TPIN in `.env`. Receipt identity is configuration-driven; it is never populated with fake customer or transaction values.

   `DEFAULT_VAT_RATE` is set to the [Zambia Revenue Authority's current 16% standard rate](https://www.zra.org.zm/tax-information/). Set it to the rate that legally applies to this business and transaction type; zero-rated or exempt supplies must not be charged the standard rate. The checkout allows an authorized cashier to adjust the rate for a sale, and the committed rate is preserved on its receipt.

4. Create the first administrator. The command prompts for a hidden password of at least 12 characters:

   ```bash
   php scripts/create-admin.php --username=admin --name="Store Administrator"
   ```

5. Point the Apache document root at this repository, enable `mod_rewrite`, and allow overrides. The app must be able to create upload subdirectories inside:

   ```text
   views/img/products/
   views/img/users/
   ```

6. Open the configured HTTPS URL and sign in with the administrator created in step 4. There are no default credentials.

## Upgrading an existing installation

Back up the database first, deploy this code over the application, configure `.env`, and apply the one-time migration:

```bash
mysqldump -u root -p posystem > posystem-before-modernization.sql
mysql -u root -p posystem < database/001_modernization.sql
```

The migration converts monetary fields to exact decimals, stores the VAT rate and cash tender/change values, adds missing expenses and audit tables, creates indexes and constraints, and normalizes legacy date/password storage. It intentionally stops on duplicate product codes, duplicate usernames, duplicate categories, or orphaned records so those integrity issues can be corrected instead of silently discarded.

Legacy fixed-salt password hashes are accepted only during the first successful login and are immediately replaced with PHP's current password hash. New and changed passwords always require at least 12 characters.

## Receipts and VAT

The browser opens the print dialog immediately after a checkout commits successfully. A receipt can also be reprinted from Sales history. The receipt reads the committed sale snapshot and never trusts totals sent by the browser.

- Unit prices come from the product record at checkout time.
- Line totals, subtotal, VAT, total, cash tendered, and change are calculated on the server.
- Card and mobile-money references are validated and stored with the sale.
- Business name, address, phone, TPIN, currency symbol, and VAT label come from `.env`.
- The print stylesheet targets 80 mm receipt paper and also works with “Save as PDF.”

## Security and data integrity

- PDO prepared statements and whitelisted query fields
- CSRF protection on every state-changing request
- Authentication and role checks on routes, AJAX, reports, receipts, and invoice exports
- Secure session cookies, session ID rotation, login throttling, and modern password hashing
- Server-side sale totals and inventory validation with row locks and transactions
- MIME/type/size validation for uploads and managed-path-only deletion
- Output encoding and JSON encoding for HTML, charts, and embedded catalog data
- CSV formula-injection protection
- Security headers, a restrictive content security policy, and protected internal directories
- Audit entries for authentication, sales, inventory, users, expenses, reports, and invoices
- Referential constraints that preserve financial history

Completed sales can only be edited or reversed by an administrator. Reversals restore inventory transactionally. Users linked to financial history are deactivated rather than deleted.

## Operations

- Back up the database and `views/img/products` / `views/img/users` together.
- Keep `APP_DEBUG=false` in production.
- Serve only over HTTPS so the session cookie receives the `Secure` flag.
- Give the runtime database user only `SELECT`, `INSERT`, `UPDATE`, and `DELETE`; use an administrator account for schema changes.
- Review low-stock alerts and the Activity log from the administrator portal.
- Keep `.env` outside version control and rotate any credential that has previously been shared in plaintext.

## Verification

Run syntax checks from the repository root:

```bash
find . -type f -name '*.php' -print0 | xargs -0 -n1 php -l
for file in views/js/*.js; do node --check "$file"; done
```

The login page can be smoke-tested without a database connection. Authenticated workflows require the schema, required PHP extensions, and a configured `.env`.

## Repository layout

```text
ajax/          Authenticated JSON endpoints
config/        Environment-backed application configuration
controllers/   Validation and application workflows
core/          Bootstrap, session, CSRF, upload, and security helpers
database/      Clean schema and legacy modernization migration
models/        Prepared database access
scripts/       CLI administration utilities
views/         Templates, modules, styles, scripts, and local browser assets
index.php      Application entry point
```

The original ZIP, plaintext login notes, seeded database dump, generated XML receipts, sample account/product images, duplicate pages, unused printer library, and unused front-end source files were intentionally removed. They are not application dependencies and should not be committed.
