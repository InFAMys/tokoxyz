<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v5
- phpunit/phpunit (PHPUNIT) - v13
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>

## Repository-specific notes (Toko XYZ)

### Database & models
- Custom primary keys everywhere — never assume `id`. Set via `#[Table(key: 'id_x')]`
  attribute (and/or `$primaryKey`): `barangs.id_barang`, `ukurans.id_ukuran`,
  `keranjang.id_keranjang`, `customers.id_cst`. Use the exact key in queries/relations.
- Soft deletes on `barangs`, `ukurans`, `pegawais`, etc. Eloquent excludes trashed rows
  by default; use `withTrashed()` when needed (e.g. cart shows trashed barang+ukuran).

### Routing & auth
- Three isolated areas, each own guard + layout:
  customer (public), `prefix('owner')` (+`guest:owner`/`auth:owner`), `prefix('pegawai')`
  (+`auth:pegawai`). Named routes scoped: `owner.*`, `pegawai.*`, `barang.*`, `keranjang.*`.
- Layouts: `resources/views/{customer,owner,pegawai}/layouts/app.blade.php`.

### Frontend / JS (critical build step)
- All custom JS is centralized in `resources/js/script.js` (single Vite module, loaded via
  `@vite` in every layout). Add page JS there, not inline in views.
- Bootstrap 5.3.8 + Font Awesome load from CDN AFTER `@vite(...)`, so the global
  `bootstrap` object is available to script.js (e.g. `bootstrap.Toast`).
- Line endings are MIXED across views (some CRLF, some LF). When editing with
  sed/grep, `$`-anchored patterns can miss CRLF lines. Prefer the edit tool.
- **Frontend changes don't reflect until `npm run build`** (Vite). Run it after any
  CSS/JS change; recompile blades with `php artisan view:cache`.

### Flash messages → toasts
- Alert boxes were replaced by Bootstrap toasts. Central partial
  `resources/views/components/toasts.blade.php` (included in the 3 layouts + standalone
  auth pages) auto-collects EVERY string flash key plus `$errors`.
  Flash keys vary per feature (`astatus`, `estatus`, `delStatus`, per-row `estatus-<id>`, …).
- Old alert blocks remain as Blade comments (`{{-- … --}}`) in views — do NOT re-introduce
  alert boxes for flash/session messages. Field-clamped `@error`/invalid-feedback messages
  stay under their inputs (not toasts).

### Money & numeric fields
- Prices: `barangs.harga` (decimal:2); optional per-ukuran `ukurans.harga_ukuran`
  (decimal:2, nullable). Cart unit price = ukuran price when set:
  `$item->ukuran?->harga_ukuran ?? $item->barang->harga`. Displayed price is a range
  (min–max of ukuran prices).
- Harga inputs are `type="text"` formatted live (id-ID thousand dots) by
  `moneyFormat()`/`toIntegerDigits()` in script.js; JS strips separators on submit because
  server validates `numeric`. DB `decimal:2` yields values like `"3000000.00"`.
- `berat` UI uses comma-decimal (input mask allows `.` or `,`); converted to dot server-side
  via `BarangController@normalizeBerat`.
- `Barang@stokReady()` = sum(`stok_ukuran`) when ukurans exist, else `barang.stok`.

### Commands
- `vendor/bin/pint --dirty --format agent` after PHP edits.
- `php artisan test --compact` (currently only the 2 Example tests).
- `npm run build` for asset changes.
