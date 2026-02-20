<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Material;
use App\Models\State;

class VendorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lagos = State::where('state', 'Lagos')->first();
        $abuja = State::where('state', 'Abuja (FCT)')->first();
        $kano = State::where('state', 'Kano')->first();

        $vendors = [
            [
                'name' => 'Buildwell Nigeria Ltd',
                'code' => 'VEN-2026-001',
                'contact_person' => 'Adeyemi Johnson',
                'email' => 'sales@buildwell.ng',
                'phone' => '08012345678',
                'address' => '15 Ikorodu Road, Yaba',
                'state_id' => $lagos?->id,
                'business_registration' => 'RC123456',
                'tax_id' => 'TIN7890123',
                'bank_name' => 'First Bank',
                'bank_account' => '2034567890',
                'rating' => 4.5,
                'status' => 'active',
            ],
            [
                'name' => 'Prime Construction Supplies',
                'code' => 'VEN-2026-002',
                'contact_person' => 'Ngozi Okafor',
                'email' => 'info@primesupplies.ng',
                'phone' => '08087654321',
                'address' => 'Plot 45, Adeola Odeku Street, Victoria Island',
                'state_id' => $lagos?->id,
                'business_registration' => 'RC234567',
                'tax_id' => 'TIN8901234',
                'bank_name' => 'GTBank',
                'bank_account' => '0123456789',
                'rating' => 4.8,
                'status' => 'active',
            ],
            [
                'name' => 'Capital Building Materials',
                'code' => 'VEN-2026-003',
                'contact_person' => 'Ibrahim Musa',
                'email' => 'contact@capitalbm.ng',
                'phone' => '09012345678',
                'address' => 'Cadastral Zone, Wuse 2',
                'state_id' => $abuja?->id,
                'business_registration' => 'RC345678',
                'tax_id' => 'TIN9012345',
                'bank_name' => 'UBA',
                'bank_account' => '1234567890',
                'rating' => 4.3,
                'status' => 'active',
            ],
            [
                'name' => 'Northern Steel & Cement Co.',
                'code' => 'VEN-2026-004',
                'contact_person' => 'Fatima Abdullahi',
                'email' => 'orders@northernsteel.ng',
                'phone' => '08098765432',
                'address' => 'Kofar Ruwa, Kano Municipal',
                'state_id' => $kano?->id,
                'business_registration' => 'RC456789',
                'tax_id' => 'TIN0123456',
                'bank_name' => 'Zenith Bank',
                'bank_account' => '2345678901',
                'rating' => 4.6,
                'status' => 'active',
            ],
            [
                'name' => 'Lagos Mega Mart',
                'code' => 'VEN-2026-005',
                'contact_person' => 'Chidi Eze',
                'email' => 'sales@lagosmegamart.com',
                'phone' => '07012345678',
                'address' => 'Alaba International Market, Ojo',
                'state_id' => $lagos?->id,
                'business_registration' => 'RC567890',
                'tax_id' => 'TIN1234567',
                'bank_name' => 'Access Bank',
                'bank_account' => '3456789012',
                'rating' => 4.2,
                'status' => 'active',
            ],
        ];

        foreach ($vendors as $vendorData) {
            $vendor = Vendor::firstOrCreate(
                ['code' => $vendorData['code']],
                $vendorData
            );

            // Assign materials with pricing
            $this->assignMaterialsToVendor($vendor);
        }
    }

    private function assignMaterialsToVendor(Vendor $vendor)
    {
        // Get some random materials and assign with pricing
        $pricingMap = [
            'MAT-CEM-001' => rand(5000, 6000),     // Dangote Cement
            'MAT-CEM-002' => rand(4800, 5800),     // BUA Cement
            'MAT-ROD-001' => rand(3500, 4500),     // Iron Rod 12mm
            'MAT-ROD-002' => rand(6000, 7000),     // Iron Rod 16mm
            'MAT-ROD-003' => rand(9000, 11000),    // Iron Rod 20mm
            'MAT-SAND-001' => rand(18000, 25000),  // Sharp Sand per ton
            'MAT-GRAN-001' => rand(22000, 28000),  // Granite 3/4
            'MAT-BLOCK-001' => rand(200, 280),     // 6" Blocks
            'MAT-BLOCK-002' => rand(280, 350),     // 9" Blocks
            'MAT-TILE-001' => rand(8000, 12000),   // Ceramic Tiles
            'MAT-ROOF-001' => rand(4500, 5500),    // Roofing Sheets
            'MAT-PAINT-001' => rand(15000, 18000), // Emulsion Paint
        ];

        foreach ($pricingMap as $materialCode => $basePrice) {
            $material = Material::where('code', $materialCode)->first();
            if ($material) {
                // Add some variance to pricing per vendor
                $variance = rand(-500, 500);
                $price = $basePrice + $variance;

                $vendor->materials()->syncWithoutDetaching([
                    $material->id => [
                        'price' => $price,
                        'currency' => 'NGN',
                        'minimum_order_quantity' => rand(1, 10),
                        'lead_time_days' => rand(1, 7),
                        'is_preferred' => rand(0, 1) == 1,
                        'valid_from' => now()->subDays(30),
                        'valid_until' => now()->addMonths(6),
                    ]
                ]);
            }
        }
    }
}
