<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use SplFileObject;

class InitSeeder extends Seeder
{
    public function run(): void
    {
        $base = database_path('seeders/data');
        $this->importCsv(DB::table('schools'),     "$base/schools.csv");
        $this->importCsv(DB::table('departments'), "$base/departments.csv");
        $this->importCsv(DB::table('oc_events'),   "$base/oc_events.csv");
        $this->importCsv(DB::table('reviews'),     "$base/reviews.csv");
    }

    private function importCsv($table, string $path): void
    {
        if (!file_exists($path)) { $this->command->warn("CSV not found: $path"); return; }
        $csv = new SplFileObject($path);
        $csv->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
        $header = null;
        foreach ($csv as $row) {
            if ($row === null or $row === [null] or $row === false) continue;
            if (!$header) { $header = $row; continue; }
            $data = array_combine($header, $row);
            if (!$data) continue;

            if (array_key_exists('tags', $data)) {
                $data['tags'] = $this->normalizeTags($data['tags'] ?? '');
            }
            if (array_key_exists('pros', $data)) {
                $data['pros'] = $this->normalizeJsonColumn($data['pros']);
            }
            if (array_key_exists('cons', $data)) {
                $data['cons'] = $this->normalizeJsonColumn($data['cons']);
            }
            if (array_key_exists('notes', $data) && ($data['notes'] ?? '') === '') {
                $data['notes'] = null;
            }
            if (array_key_exists('is_published', $data)) {
                $data['is_published'] = (int) ($data['is_published'] !== '0');
            }
            if (array_key_exists('created_at', $data) && ($data['created_at'] ?? '') === '') {
                $data['created_at'] = now();
            }
            if (array_key_exists('updated_at', $data) && ($data['updated_at'] ?? '') === '') {
                $data['updated_at'] = $data['created_at'] ?? now();
            }

            $table->insert($data);
        }
    }

    private function normalizeTags(?string $value): string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }
        return collect(explode(';', $value))
            ->map(fn($item) => trim($item))
            ->filter()
            ->implode(';');
    }

    private function normalizeJsonColumn(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }
        $decoded = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $filtered = array_values(array_filter(array_map(function ($item) {
                return is_string($item) ? trim($item) : '';
            }, $decoded)));
            return empty($filtered) ? null : json_encode($filtered, JSON_UNESCAPED_UNICODE);
        }
        $items = array_values(array_filter(array_map('trim', preg_split('/[;\n]/', $trimmed))));
        return empty($items) ? null : json_encode($items, JSON_UNESCAPED_UNICODE);
    }
}
