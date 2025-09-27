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
            if (isset($data['tags'])) {
              $data['tags'] = $data['tags'] !== '' && str_contains($data['tags'], ';')
                ? json_encode(explode(';', $data['tags']), JSON_UNESCAPED_UNICODE)
                : ($data['tags'] ? json_encode([$data['tags']], JSON_UNESCAPED_UNICODE) : json_encode([]));
            }
            $table->insert($data);
        }
    }
}
