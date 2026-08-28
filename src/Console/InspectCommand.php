<?php

declare(strict_types=1);

namespace Synetro\Fuse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InspectCommand extends Command
{
    protected $signature = 'fuse:inspect {model? : Model to inspect}';
    protected $description = 'Inspect a model and its related components';

    public function handle(): int
    {
        $model = $this->argument('model');

        if (!$model) {
            $this->error('Please specify a model name.');
            return Command::FAILURE;
        }

        $this->info("Inspecting: {$model}");
        $this->line('==================');

        try {
            $instance = new $model();
            $relations = get_class_methods($instance);

            $this->info('Relations:');
            foreach ($relations as $relation) {
                try {
                    $result = $instance->{$relation}();
                    if ($result instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
                        $this->line("  - {$relation}()");
                    }
                } catch (\Throwable $e) {
                    // skip non-relation methods
                }
            }
        } catch (\Throwable $e) {
            $this->error('Model not found: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
