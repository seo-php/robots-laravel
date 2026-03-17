## Plan: `seophp/robots-laravel` — Laravel Integration Package

A separate Composer package that bridges `seophp/robots` into Laravel. It auto-registers a `GET /robots.txt` route, lets users define robots rules via a callback class, provides environment-based defaults (disallow-all on non-production), and includes an `artisan robots:generate` command for static file generation.

### Package Structure

```
seophp/robots-laravel/
├── composer.json
├── config/
│   └── robots.php
├── src/
│   ├── RobotsServiceProvider.php
│   ├── Http/
│   │   ├── RobotsTxtController.php
│   │   └── RobotsTxtResponse.php
│   ├── Contracts/
│   │   └── RobotsTxtDefinition.php
│   ├── DefaultRobotsTxtDefinition.php
│   └── Console/
│       └── GenerateRobotsTxtCommand.php
├── tests/
├── ecs.php
├── phpstan.neon.dist
├── rector.php
└── phpunit.xml.dist
```

### Steps

**1. Create `RobotsDefinition` contract** — src/Contracts/RobotsDefinition.php

- Interface with a single method: `define(RobotsTxtBuilder $builder): void`
- Users implement this to programmatically configure their robots.txt using the full fluent builder API from `seophp/robots`

**2. Create `DefaultRobotsDefinition`** — src/DefaultRobotsDefinition.php

- Implements `RobotsDefinition`
- Checks `$this->app->environment('production')`:
  - **Production**: allow all (single group with `User-agent: *` and empty `Disallow:`)
  - **Non-production**: disallow everything (`User-agent: *`, `Disallow: /`)
- This is the fallback when the user hasn't registered their own implementation

**3. Create `config/robots.php`** — config/robots.php

- `'enabled'` — bool, whether the route is registered (default: `true`). Set to `false` if user wants to handle it themselves or use a static file
- `'definition'` — FQCN of the `RobotsDefinition` implementation (default: `DefaultRobotsDefinition::class`)

**4. Create `RobotsTxtResponse`** — src/Http/RobotsTxtResponse.php

- Constructor `__construct` with a promoted property `$robots` with type `RobotsTxt`
- Implements the `Responsable` interface from Laravel
- Renders via `RobotsTxtRenderer`
- Returns a `Response` with `Content-Type: text/plain; charset=UTF-8` and status `200`

**4. Create `RobotsController`** — src/Http/RobotsController.php

- Single `__invoke` action
- Resolves `RobotsDefinition` from the container
- Creates a new `RobotsTxtBuilder`, passes it to `definition->define($builder)`, calls `$builder->build()`
- Returns a `RobotsTxtResponse`

**5. Create `GenerateRobotsTxtCommand`** — src/Console/GenerateRobotsTxtCommand.php

- Artisan command: `robots:generate`
- Option `--path` (default: `public/robots.txt`)
- Resolves the `RobotsDefinition`, builds, renders, writes to file
- Displays success message with the output path
- Option `--force` to overwrite existing file without confirmation

**6. Create `RobotsServiceProvider`** — src/RobotsServiceProvider.php

- **`register()`**:
  - Merge config from `config/robots.php`
  - Bind `RobotsDefinition` interface → configured implementation class (singleton)
- **`boot()`**:
  - If `config('robots.enabled')` is `true`, register `Route::get('/robots.txt', RobotsController::class)` — no middleware group (bare route, no session/CSRF overhead)
  - Register `GenerateRobotsTxtCommand` when running in console
  - Publish config: `$this->publishes([...], 'robots-config')`

**7. Tooling config files**

- Mirror conventions from `seophp/robots`: same ECS ruleset (`eolica/coding-standard`), PHPStan level 10, Rector for PHP 8.4, Pest with parallel execution
- Dev-require `orchestra/testbench` for Laravel package testing

**8. Tests**

- Test `DefaultRobotsDefinition` produces correct output for production vs non-production
- Test `RobotsController` returns `text/plain` response with correct content
- Test route is registered when `enabled = true`, not when `false`
- Test `robots:generate` command writes file to disk
- Test custom `RobotsDefinition` implementation is resolved from container

### Verification

- `composer test` — runs Pest test suite with Testbench
- `composer ecs` — coding standards
- `composer phpstan` — static analysis at level 10
- Manual: install in a fresh Laravel app via `composer require seophp/robots-laravel`, hit `/robots.txt`, verify output changes between environments

### Decisions

- **Callback-based over config-driven**: users implement `RobotsDefinition` interface for full builder API access, rather than defining arrays in config
- **Bare route (no middleware)**: `/robots.txt` is a public, stateless endpoint — no need for session, CSRF, or auth middleware overhead
- **Package name**: `seophp/robots-laravel` per user preference
- **No facade**: not selected for initial version, can be added later if needed
