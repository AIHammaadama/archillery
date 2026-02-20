<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $path = 'materials.csv';

        if (!Storage::disk('local')->exists($path)) {
            $this->command?->error("CSV not found at storage/app/{$path}");
            return;
        }

        $csv = Storage::disk('local')->get($path);
        $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv);
        $lines = preg_split("/\r\n|\n|\r/", trim($csv));

        if (!$lines || count($lines) < 2) {
            $this->command?->error("CSV appears empty or missing data rows.");
            return;
        }

        // Read header
        $header = str_getcsv(array_shift($lines));

        $header = array_values(array_filter(array_map(function ($h) {
            $h = preg_replace('/^\xEF\xBB\xBF/', '', $h); // remove BOM from each header cell
            $h = strtolower(trim($h));
            return $h;
        }, $header), fn($h) => $h !== ''));

        // Expected columns (minimum)
        $required = ['name', 'category', 'unit_of_measurement'];
        foreach ($required as $col) {
            if (!in_array($col, $header, true)) {
                $this->command?->error("Missing required column: {$col}");
                $this->command?->line("Header found: " . implode(', ', $header));
                return;
            }
        }

        $categories = array_keys(config('materials.categories', []));
        $uomLabels  = array_values(config('materials.units_of_measurement', []));

        // Build a map: header_name => index
        $idx = array_flip($header);

        $inserted = 0;
        $skippedDuplicates = 0;
        $invalidRows = 0;
        $errors = [];

        $uomMap = [];
        foreach (config('materials.units_of_measurement', []) as $code => $label) {
            $uomMap[$label] = $code;
        }

        DB::transaction(function () use (
            $lines,
            $idx,
            $categories,
            $uomMap,
            &$inserted,
            &$skippedDuplicates,
            &$invalidRows,
            &$errors
        ) {
            foreach ($lines as $lineNumber => $line) {
                if (trim($line) === '') continue;

                $row = str_getcsv($line);

                $name = trim($row[$idx['name']] ?? '');
                $category = trim($row[$idx['category']] ?? '');
                $uomLabel = trim($row[$idx['unit_of_measurement']] ?? '');

                $description = isset($idx['description']) ? trim($row[$idx['description']] ?? '') : null;
                $specificationsRaw = isset($idx['specifications']) ? trim($row[$idx['specifications']] ?? '') : null;
                $isActiveRaw = isset($idx['is_active']) ? trim($row[$idx['is_active']] ?? '') : '';

                if ($name === '' || $category === '' || $uomLabel === '') {
                    $invalidRows++;
                    $errors[] = "Line " . ($lineNumber + 2) . ": Missing name/category/unit_of_measurement";
                    continue;
                }

                if (!in_array($category, $categories, true)) {
                    $invalidRows++;
                    $errors[] = "Line " . ($lineNumber + 2) . ": Invalid category '{$category}'";
                    continue;
                }

                // ✅ Convert label -> code (Units (units) => units)
                $uomCode = $uomMap[$uomLabel] ?? null;
                if (!$uomCode) {
                    $invalidRows++;
                    $errors[] = "Line " . ($lineNumber + 2) . ": Invalid unit_of_measurement '{$uomLabel}'";
                    continue;
                }

                $specifications = null;
                if ($specificationsRaw !== null && $specificationsRaw !== '') {
                    $decoded = json_decode($specificationsRaw, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $invalidRows++;
                        $errors[] = "Line " . ($lineNumber + 2) . ": Invalid JSON in specifications";
                        continue;
                    }
                    $specifications = $decoded;
                }

                $isActive = true;
                if ($isActiveRaw !== '') {
                    $isActive = in_array(strtolower($isActiveRaw), ['1', 'true', 'yes'], true);
                }

                // ✅ Dedup uses the stored value (uomCode)
                $material = Material::firstOrNew([
                    'name' => $name,
                    'category' => $category,
                    'unit_of_measurement' => $uomCode,
                ]);

                if (!$material->exists) {
                    $material->code = $this->generateMaterialCode();
                    $inserted++;
                } else {
                    $skippedDuplicates++;
                }

                $material->description = $description ?: null;
                $material->specifications = $specifications;
                $material->is_active = $isActive;
                $material->save();
            }
        });

        $this->command?->info("✅ Material CSV Import Completed");
        $this->command?->line("Inserted: {$inserted}");
        $this->command?->line("Skipped duplicates: {$skippedDuplicates}");
        $this->command?->line("Invalid rows: {$invalidRows}");

        if (!empty($errors)) {
            $this->command?->warn("---- Errors (first 30) ----");
            foreach (array_slice($errors, 0, 30) as $err) {
                $this->command?->line($err);
            }
        }
    }

    private function generateMaterialCode(): string
    {
        $year = date('Y');
        $padding = (int) config('materials.code_sequence_padding', 4);

        $latestMaterial = Material::where('code', 'like', "MAT-{$year}-%")
            ->orderBy('code', 'desc')
            ->first();

        $newSequence = 1;
        if ($latestMaterial) {
            $parts = explode('-', $latestMaterial->code);
            $lastSequence = isset($parts[2]) ? (int) $parts[2] : 0;
            $newSequence = $lastSequence + 1;
        }

        $sequence = str_pad((string)$newSequence, $padding, '0', STR_PAD_LEFT);

        return "MAT-{$year}-{$sequence}";
    }
}
