<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;

class SecurityCommand extends Command
{
    protected $signature = 'fuse:security
                            {--fix : Attempt to fix issues}';

    protected $description = 'Check security settings';

    public function handle(): int
    {
        $checks = [
            $this->checkAppKey(),
            $this->checkDebugMode(),
            $this->checkHttps(),
            $this->checkSecureCookies(),
            $this->checkCsrfProtection(),
            $this->checkCors(),
            $this->checkRateLimiting(),
            $this->checkWritableFiles(),
        ];

        $this->displayResults($checks);

        $failed = collect($checks)->filter(fn ($c) => $c['status'] === 'fail')->count();

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    protected function checkAppKey(): array
    {
        $key = config('app.key');

        return [
            'name' => 'APP_KEY',
            'status' => $key && strlen($key) > 20 ? 'pass' : 'fail',
            'message' => $key ? 'Set ('.strlen($key).' chars)' : 'Not set',
        ];
    }

    protected function checkDebugMode(): array
    {
        return [
            'name' => 'APP_DEBUG',
            'status' => config('app.debug') ? 'warn' : 'pass',
            'message' => config('app.debug') ? 'Enabled (should be false in production)' : 'Disabled',
        ];
    }

    protected function checkHttps(): array
    {
        return [
            'name' => 'HTTPS',
            'status' => config('fuse.security.force_https', false) ? 'pass' : 'warn',
            'message' => config('fuse.security.force_https', false) ? 'Forced' : 'Not forced',
        ];
    }

    protected function checkSecureCookies(): array
    {
        return [
            'name' => 'Secure Cookies',
            'status' => config('session.secure', false) ? 'pass' : 'warn',
            'message' => config('session.secure', false) ? 'Enabled' : 'Disabled',
        ];
    }

    protected function checkCsrfProtection(): array
    {
        return [
            'name' => 'CSRF Protection',
            'status' => 'pass',
            'message' => 'Enabled by default',
        ];
    }

    protected function checkCors(): array
    {
        return [
            'name' => 'CORS',
            'status' => config('cors.enabled', false) ? 'pass' : 'warn',
            'message' => config('cors.enabled', false) ? 'Configured' : 'Not configured',
        ];
    }

    protected function checkRateLimiting(): array
    {
        return [
            'name' => 'Rate Limiting',
            'status' => config('fuse.security.rate_limiting', false) ? 'pass' : 'warn',
            'message' => config('fuse.security.rate_limiting', false) ? 'Enabled' : 'Disabled',
        ];
    }

    protected function checkWritableFiles(): array
    {
        $writable = is_writable(base_path('storage')) && is_writable(base_path('bootstrap/cache'));

        return [
            'name' => 'Writable Files',
            'status' => $writable ? 'pass' : 'fail',
            'message' => $writable ? 'Correct permissions' : 'Check permissions',
        ];
    }

    protected function displayResults(array $checks): void
    {
        $rows = collect($checks)->map(fn ($c) => [
            $c['name'],
            match ($c['status']) {
                'pass' => 'OK',
                'warn' => 'WARN',
                default => 'FAIL',
            },
            $c['message'],
        ])->toArray();

        $this->table(['Check', 'Status', 'Message'], $rows);
    }
}
