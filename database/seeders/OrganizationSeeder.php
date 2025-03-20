<?php

namespace Database\Seeders;

use App\Models\Org\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'Dutch Navy Seals',
                'description' => 'Elite special operations force of the Netherlands Maritime Special Operations Forces.',
                'slug' => 'dutch-navy-seals',
            ],
            [
                'name' => 'Royal Netherlands Marine Corps',
                'description' => 'Marine corps and amphibious infantry component of the Royal Netherlands Navy.',
                'slug' => 'royal-netherlands-marine-corps',
            ],
            [
                'name' => 'Netherlands Maritime Special Operations Forces',
                'description' => 'Special operations component of the Royal Netherlands Navy.',
                'slug' => 'netherlands-maritime-special-operations-forces',
            ],
        ];

        foreach ($organizations as $org) {
            Organization::create($org);
        }
    }
}
