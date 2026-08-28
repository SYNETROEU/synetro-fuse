# Contributing to Fuse

Thank you for your interest in contributing to Fuse! This document provides guidelines and instructions for contributing.

## Code of Conduct

Be respectful, inclusive, and constructive. We reserve the right to remove comments and contributions that do not align with our values.

## How to Contribute

### Reporting Bugs

- Search existing issues before opening a new one
- Include Laravel version, PHP version, and OS
- Provide a minimal reproduction case
- Include relevant error messages and stack traces

### Suggesting Features

- Open an issue with the `enhancement` label
- Describe the problem you're solving, not just the solution
- Explain why this fits Fuse's philosophy of reducing boilerplate

### Pull Requests

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes
4. Run tests: `vendor/bin/phpunit`
5. Run linting: `vendor/bin/pint`
6. Commit with a clear message: `feat: add X` or `fix: resolve Y`
7. Push to your fork
8. Open a Pull Request against `main`

## Development

```bash
composer install
vendor/bin/phpunit
vendor/bin/pint
```

## Commit Conventions

- `feat:` — new feature
- `fix:` — bug fix
- `docs:` — documentation changes
- `refactor:` — code refactoring
- `test:` — adding or updating tests
- `chore:` — maintenance tasks

## Questions?

Open an issue or reach out at support@synetro.eu.
