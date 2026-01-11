<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str; // Import this helper

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Construction Permit Application',
                'description' => 'Complete assistance with construction permit applications, including document preparation and follow-up.',
                'price' => 250.00,
                'icon_color' => 'bg-success',
                'icon_class' => 'bi-tools',
            ],
            [
                'name' => 'Diiwaangelinta Shirkadaha',
                'description' => 'Professional registration for developers and shared building companies.',
                'price' => 180.00,
                'icon_color' => 'bg-mu-blue',
                'icon_class' => 'bi-person-badge',
            ],
            [
                'name' => 'Project Registration',
                'description' => 'Assistance with project registration processes, including documentation and compliance checks.',
                'price' => 150.00,
                'icon_color' => 'bg-primary',
                'icon_class' => 'bi-file-earmark-medical',
            ],
            [
                'name' => 'Business License Processing',
                'description' => 'End-to-end business license processing services, from application to approval.',
                'price' => 200.00,
                'icon_color' => 'bg-info',
                'icon_class' => 'bi-card-checklist',
            ],
            [
                'name' => 'Property Transfer Services',
                'description' => 'Complete property transfer services including documentation and legal compliance.',
                'price' => 300.00,
                'icon_color' => 'bg-dark',
                'icon_class' => 'bi-arrow-left-right',
            ],
            [
                'name' => 'Ownership Certificate',
                'description' => 'Issuance of official apartment and unit ownership documentation.',
                'price' => 175.00,
                'icon_color' => 'bg-primary',
                'icon_class' => 'bi-building-check',
            ],
        ];

        $slugMap = [
            'Construction Permit Application' => 'construction-permit-application',
            'Diiwaangelinta Shirkadaha' => 'developer-registration',
            'Project Registration' => 'project-registration',
            'Business License Processing' => 'business-license',
            'Property Transfer Services' => 'property-transfer-services',
            'Ownership Certificate' => 'ownership-certificate',
        ];

        foreach ($services as $service) {
            $explicitSlug = $slugMap[$service['name']] ?? null;
            $slug = $explicitSlug ?: Str::slug($service['name']);
            $service['slug'] = $slug;
            Service::updateOrCreate(['name' => $service['name']], $service);
        }
    }
}
