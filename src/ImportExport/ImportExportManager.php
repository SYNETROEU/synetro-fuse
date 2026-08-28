<?php

declare(strict_types=1);

namespace Synetro\Fuse\ImportExport;

class ImportExportManager
{
    public function import(string $model, mixed $file, ?callable $onEach = null): array
    {
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
                $rows[] = implode(',', array_map(fn ($v) => "\"{$v}\"", $record->toArray()));
            }
        });

        return implode("\n", array_merge([implode(',', $headers)], $rows));
    }

    protected function parseFile(mixed $file): array
    {
        if (is_string($file)) {
            $content = file_get_contents($file);
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            if ($ext === 'json') {
                return json_decode($content, true) ?? [];
            }

            return array_map('str_getcsv', explode("\n", $content));
        }

        return $file;
    }
}
