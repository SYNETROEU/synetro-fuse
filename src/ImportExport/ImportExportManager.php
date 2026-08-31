<?php

declare(strict_types=1);

namespace Synetro\Fuse\ImportExport;

use Illuminate\Support\Facades\DB;

class ImportExportManager
{
    public function import(string $model, mixed $file, ?callable $onEach = null): array
    {
        return DB::transaction(function () use ($model, $file, $onEach) {
            $records = $this->parseFile($file);
            $imported = 0;
            $failed = [];

            foreach ($records as $index => $row) {
                try {
                    $instance = new $model($row);
                    $instance->save();
                    $imported++;

                    if ($onEach) {
                        $onEach($instance, $index);
                    }
                } catch (\Throwable $e) {
                    $failed[] = ['row' => $index + 1, 'error' => $e->getMessage()];
                }
            }

            return ['imported' => $imported, 'failed' => $failed];
        });
    }

    public function export(string $model, string $format = 'csv'): string
    {
        $query = $model::query();

        if ($format === 'json') {
            return json_encode($query->get());
        }

        $headers = [];
        $rows = [];

        $query->chunk(100, function ($records) use (&$headers, &$rows) {
            foreach ($records as $record) {
                if (empty($headers)) {
                    $headers = array_keys($record->toArray());
                }
                // Properly escape CSV fields using fputcsv-style escaping
                $escapedRow = array_map(function ($v) {
                    return '"' . str_replace('"', '""', $v) . '"';
                }, $record->toArray());
                $rows[] = implode(',', $escapedRow);
            }
        });

        return implode("\n", array_merge([implode(',', $headers)], $rows));
    }

    protected function parseFile(mixed $file): array
    {
        if (is_string($file)) {
            $content = file_get_contents($file);
            if ($content === false) {
                return [];
            }

            $ext = pathinfo($file, PATHINFO_EXTENSION);

            if ($ext === 'json') {
                return json_decode($content, true) ?? [];
            }

            $lines = explode("\n", $content);
            if (empty($lines)) {
                return [];
            }

            // Parse CSV: first line is headers
            $headers = str_getcsv(trim($lines[0]));
            $records = [];

            for ($i = 1; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                if (empty($line)) {
                    continue;
                }

                $values = str_getcsv($line);
                $records[] = array_combine($headers, $values);
            }

            return $records;
        }

        return $file;
    }
}
