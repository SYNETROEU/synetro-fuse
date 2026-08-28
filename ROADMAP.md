# Roadmap

## Completed

- [x] Package skeleton and service provider
- [x] Facade and main API class
- [x] Configuration system (config/fuse.php)
- [x] Install command and artisan commands
- [x] Database migrations (configs, secrets, features, audits, webhooks, files)
- [x] DB-backed configuration with caching
- [x] Secrets manager with encryption and log redaction
- [x] Feature flags with rollout support
- [x] CRUD Resource system
- [x] Query system with allow-lists
- [x] Actions and ActionManager
- [x] Pipelines / workflow orchestration
- [x] API response layer with envelope
- [x] Health checks (database, cache, queue, storage)
- [x] Security manager
- [x] Logging, metrics, and notifications
- [x] Webhooks (outgoing and incoming)
- [x] Audit logging with traits
- [x] Caching helpers
- [x] File attachment helpers
- [x] Testing helpers (Flow, FakeManager)
- [x] Code generators and stubs
- [x] OpenAPI generation command
- [x] Application inspection commands

## In Progress

- [ ] Full CRUD generator integration
- [ ] Database-backed secrets (instead of file-based)
- [ ] Feature flag persistence (database-driven)
- [ ] Config metadata for admin UI

## Planned

- [ ] Multi-tenancy integration (`Fuse::for($tenant)`)
- [ ] Admin UI metadata for settings
- [ ] Image processing (optional, via intervention/image)
- [ ] Advanced query filters: `whereBetween`, `whereDate`, `whereNull`, `groupBy`, `aggregate`
- [ ] Scheduled activation for feature flags
- [ ] Webhook delivery logging and replay
- [ ] `fuse:deploy-check` command
- [ ] Module system (`fuse:make-module Billing`)
- [ ] OpenAPI inference from Form Requests
- [ ] Automatic API documentation from resources

## Philosophy

We will **not** add features that merely rename Laravel APIs. Every feature must answer:

> What annoying boilerplate does this eliminate?

If the answer is unclear, we won't implement it.
