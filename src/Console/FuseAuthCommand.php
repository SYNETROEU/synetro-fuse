<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FuseAuthCommand extends Command
{
    protected $signature = 'fuse:auth
                            {--force : Force overwrite}
                            {--with-2fa : Include 2FA scaffolding}
                            {--with-api : Include API auth scaffolding}';

    protected $description = 'Scaffold authentication (register, login, logout, password reset, email verification, 2FA, API auth)';

    public function handle(): int
    {
        $this->scaffoldControllers();
        $this->scaffoldRoutes();
        $this->scaffoldMiddleware();

        $this->info('Auth scaffolding complete.');
        $this->info('Run `php artisan migrate` to create the users table.');

        return Command::SUCCESS;
    }

    protected function scaffoldControllers(): void
    {
        $controllers = [
            'RegisterController' => $this->controllerStub('register'),
            'LoginController' => $this->controllerStub('login'),
            'LogoutController' => $this->controllerStub('logout'),
            'ForgotPasswordController' => $this->controllerStub('forgot-password'),
            'ResetPasswordController' => $this->controllerStub('reset-password'),
            'VerificationController' => $this->controllerStub('verification'),
        ];

        if ($this->option('with-2fa')) {
            $controllers['TwoFactorController'] = $this->controllerStub('two-factor');
        }

        if ($this->option('with-api')) {
            $controllers['ApiAuthController'] = $this->controllerStub('api-auth');
        }

        foreach ($controllers as $name => $stub) {
            $path = app_path("Http/Controllers/Auth/{$name}.php");
            $dir = dirname($path);

            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            if (File::exists($path) && ! $this->option('force')) {
                $this->warn("Skipped {$name} (already exists)");

                continue;
            }

            File::put($path, $stub);
            $this->info("Created: {$name}");
        }
    }

    protected function scaffoldRoutes(): void
    {
        $routesPath = base_path('routes/auth.php');

        if (! File::exists($routesPath) || $this->option('force')) {
            File::put($routesPath, $this->routesStub());
            $this->info('Created: routes/auth.php');
        } else {
            $this->warn('Skipped routes/auth.php (already exists)');
        }
    }

    protected function scaffoldMiddleware(): void
    {
        $middlewarePath = app_path('Http/Middleware/EnsureEmailIsVerified.php');
        $dir = dirname($middlewarePath);

        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        if (! File::exists($middlewarePath) || $this->option('force')) {
            File::put($middlewarePath, $this->middlewareStub('EnsureEmailIsVerified'));
            $this->info('Created: EnsureEmailIsVerified middleware');
        }
    }

    protected function controllerStub(string $type): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Http\\Controllers\\Auth;

use App\\Http\\Controllers\\Controller;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Auth;

class {$this->studly($type)}Controller extends Controller
{
    public function __construct()
    {
        \$this->middleware('guest')->except('logout');
    }

    public function show()
    {
        return view('auth.{$type}');
    }

    public function handle(Request \$request)
    {
        \$request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(\$request->only('email', 'password'))) {
            \$request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request \$request)
    {
        Auth::logout();
        \$request->session()->invalidate();
        \$request->session()->regenerateToken();

        return redirect('/');
    }
}
PHP;
    }

    protected function routesStub(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    RegisterController,
    LoginController,
    LogoutController,
    ForgotPasswordController,
    ResetPasswordController,
    VerificationController
};

Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'handle']);

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'handle']);

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'handle'])->name('password.email');

Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'handle'])->name('password.update');

Route::get('/email/verify', [VerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('auth')->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend'])->middleware('auth')->name('verification.resend');
PHP;
    }

    protected function middlewareStub(string $name): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Auth;

class {$name}
{
    public function handle(Request \$request, Closure \$next)
    {
        if (!\$request->user()?->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return \$next(\$request);
    }
}
PHP;
    }

    protected function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }
}
