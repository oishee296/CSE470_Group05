<?php

namespace Database\Seeders;

use App\Models\Disaster;
use App\Models\Inventory;
use App\Models\ReliefRequest;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use RuntimeException;

class ShareDataSeeder extends Seeder
{
    /**
     * Import R18/R19 CSV datasets from datasets/ directory.
     * Re-runnable: avoids duplicates by checking existing records.
     */
    public function run(): void
    {
        $base = base_path('datasets');
        $this->importReliefRequests($base . DIRECTORY_SEPARATOR . 'R18_relief_requests.csv', false);
        $this->importInventory($base . DIRECTORY_SEPARATOR . 'R18_inventory.csv');
        $this->importReliefRequests($base . DIRECTORY_SEPARATOR . 'R19_emergency_requests.csv', true);
    }

    private function readCsv(string $path): array
    {
        if (!file_exists($path)) {
            throw new RuntimeException("CSV file not found: {$path}");
        }
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException("Unable to open CSV file: {$path}");
        }
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            throw new RuntimeException("CSV file is empty or invalid: {$path}");
        }
        $header = array_map('trim', $header);
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) === 1 && trim($row[0]) === '') {
                continue;
            }
            if (count($row) !== count($header)) {
                fclose($handle);
                throw new RuntimeException("Invalid column count in {$path}: " . implode(',', $row));
            }
            $rows[] = array_combine($header, array_map('trim', $row));
        }
        fclose($handle);
        return $rows;
    }

    private function getOrCreateDisaster(string $name): Disaster
    {
        return Disaster::firstOrCreate(
            ['name' => $name],
            [
                'type' => 'Flood',
                'description' => 'Imported for SHARE dataset demonstration (August 2024 Eastern Bangladesh flood scenario).',
                'start_date' => '2024-08-20',
                'end_date' => null,
                'status' => 'Active',
            ]
        );
    }

    private function getOrCreateZone(Disaster $disaster, string $zoneName): Zone
    {
        // CSV uses zone name (e.g. Feni). Map to district/area/sub-district simply.
        return Zone::firstOrCreate(
            [
                'disaster_id' => $disaster->id,
                'district' => $zoneName,
                'area_name' => $zoneName,
            ],
            [
                'sub_district' => $zoneName . ' Sadar',
                'severity_level' => 'High',
                'priority_tier' => 'High',
            ]
        );
    }

    private function importInventory(string $path): void
    {
        foreach ($this->readCsv($path) as $row) {
            foreach (['disaster_name','zone','resource_name','quantity_in_stock'] as $col) {
                if (!isset($row[$col]) || $row[$col] === '') {
                    throw new RuntimeException("Missing '{$col}' in {$path}");
                }
            }
            $disaster = $this->getOrCreateDisaster($row['disaster_name']);
            $zone = $this->getOrCreateZone($disaster, $row['zone']);
            $qty = (int) $row['quantity_in_stock'];
            if ($qty < 0) {
                throw new RuntimeException("Invalid quantity_in_stock in {$path}");
            }

            Inventory::updateOrCreate(
                [
                    'disaster_id' => $disaster->id,
                    'zone_id' => $zone->id,
                    'resource_name' => $row['resource_name'],
                ],
                ['quantity_in_stock' => $qty]
            );
        }
    }

    private function importReliefRequests(string $path, bool $isEmergency): void
    {
        foreach ($this->readCsv($path) as $row) {
            if (!isset($row['disaster_name'], $row['zone'], $row['priority'], $row['status'], $row['created_at'])) {
                throw new RuntimeException("Missing required columns in {$path}");
            }
            $disaster = $this->getOrCreateDisaster($row['disaster_name']);
            $zone = $this->getOrCreateZone($disaster, $row['zone']);
            $createdAt = Carbon::parse($row['created_at']);
            $resolvedAt = (!empty($row['resolved_at'])) ? Carbon::parse($row['resolved_at']) : null;

            $data = [
                'disaster_id' => $disaster->id,
                'zone_id' => $zone->id,
                'priority' => $row['priority'],
                'status' => $row['status'],
                'resolved_at' => $resolvedAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if ($isEmergency) {
                $data['request_type'] = $row['request_type'] ?? null;
                $data['description'] = $row['description'] ?? null;
                $data['resource_name'] = null;
                $data['quantity_requested'] = null;
                $unique = [
                    'disaster_id' => $disaster->id,
                    'zone_id' => $zone->id,
                    'request_type' => $data['request_type'],
                    'description' => $data['description'],
                    'created_at' => $createdAt,
                ];
            } else {
                $data['request_type'] = null;
                $data['description'] = null;
                $data['resource_name'] = $row['resource_name'] ?? null;
                $data['quantity_requested'] = isset($row['quantity_requested']) ? (int) $row['quantity_requested'] : null;
                $unique = [
                    'disaster_id' => $disaster->id,
                    'zone_id' => $zone->id,
                    'resource_name' => $data['resource_name'],
                    'quantity_requested' => $data['quantity_requested'],
                    'priority' => $data['priority'],
                    'status' => $data['status'],
                    'created_at' => $createdAt,
                ];
            }

            $existing = ReliefRequest::where($unique)->first();
            if ($existing) {
                $existing->fill($data);
                // Preserve escalation tracking if already set; do not rewrite timestamps logic beyond CSV values.
                $existing->save();
            } else {
                ReliefRequest::create(array_merge($data, [
                    'last_escalated_at' => null,
                    'escalation_level' => 0,
                ]));
            }
        }
    }
}
