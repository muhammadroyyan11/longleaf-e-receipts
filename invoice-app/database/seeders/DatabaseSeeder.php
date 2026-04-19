<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user
        $user = User::firstOrCreate(
            ['email' => 'demo@invoiceapp.com'],
            [
                'name'     => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        // Sample Invoice 1 - PAID
        $inv1 = Invoice::create([
            'user_id'        => $user->id,
            'invoice_number' => 'INV-2139234',
            'status'         => 'paid',
            'invoice_date'   => now()->subDays(3),
            'client_company' => 'Longleaf',
            'client_name'    => 'Muhammad Royyan',
            'client_address' => 'Kebonagung Residence H32, Dusun Sonotengah',
            'client_city'    => 'pakisaji',
            'client_province'=> 'Malang, Jawa Timur',
            'client_postal_code' => '65162',
            'client_country' => 'Indonesia',
            'company_name'   => 'PT Deneva',
            'company_address'=> "Genius Idea Coworking And Office Space Yogyakarta\nJl. Magelang, Cokrodiningratan, Jetis, Kota Yogyakarta\nDaerah Istimewa Yogyakarta, 55233",
            'company_npwp'   => '80.820.685.8-542.000',
        ]);

        $inv1->items()->createMany([
            ['description' => 'Domain Registration - longleaf.id - 2 Year/s (16/04/2026 - 17/04/2028) + DNS Management', 'amount' => 420000, 'is_taxed' => true],
            ['description' => 'Domain Registration - longleaf.my.id - 1 Year/s (16/04/2026 - 17/04/2027) + DNS Management' . "\n" . 'Free Domain Bundle with longleaf.id', 'amount' => 0, 'is_taxed' => false],
            ['description' => 'Administration Fee', 'amount' => 2013, 'is_taxed' => false],
        ]);
        $inv1->recalculate();

        $inv1->transactions()->create([
            'transaction_date' => now()->subDays(3),
            'gateway'          => 'QRIS',
            'transaction_id'   => '8f615719-04fc-457d-a0d9-c7753e622716',
            'amount'           => $inv1->total,
        ]);

        // Sample Invoice 2 - UNPAID
        $inv2 = Invoice::create([
            'user_id'        => $user->id,
            'invoice_number' => 'INV-2139300',
            'status'         => 'unpaid',
            'invoice_date'   => now()->subDay(),
            'client_name'    => 'Budi Santoso',
            'client_address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
            'client_city'    => 'Jakarta Pusat',
            'client_province'=> 'DKI Jakarta',
            'client_postal_code' => '10220',
            'client_country' => 'Indonesia',
            'company_name'   => 'PT Deneva',
            'company_address'=> "Genius Idea Coworking And Office Space Yogyakarta\nJl. Magelang, Cokrodiningratan, Jetis, Kota Yogyakarta",
            'company_npwp'   => '80.820.685.8-542.000',
        ]);

        $inv2->items()->createMany([
            ['description' => 'Web Hosting - Business Plan - 1 Year/s', 'amount' => 350000, 'is_taxed' => true],
            ['description' => 'SSL Certificate - 1 Year/s', 'amount' => 150000, 'is_taxed' => true],
        ]);
        $inv2->recalculate();

        // Sample Invoice 3 - PENDING
        $inv3 = Invoice::create([
            'user_id'        => $user->id,
            'invoice_number' => 'INV-2139350',
            'status'         => 'pending',
            'invoice_date'   => now(),
            'client_company' => 'CV Maju Bersama',
            'client_name'    => 'Siti Rahayu',
            'client_address' => 'Jl. Gatot Subroto No. 12, Bandung',
            'client_city'    => 'Bandung',
            'client_province'=> 'Jawa Barat',
            'client_postal_code' => '40252',
            'client_country' => 'Indonesia',
            'company_name'   => 'PT Deneva',
            'company_address'=> "Genius Idea Coworking And Office Space Yogyakarta\nJl. Magelang, Cokrodiningratan, Jetis, Kota Yogyakarta",
            'company_npwp'   => '80.820.685.8-542.000',
        ]);

        $inv3->items()->createMany([
            ['description' => 'VPS Server - 2 Core, 4GB RAM - 1 Month', 'amount' => 250000, 'is_taxed' => true],
            ['description' => 'Setup & Konfigurasi Server', 'amount' => 500000, 'is_taxed' => false],
        ]);
        $inv3->recalculate();
    }
}
