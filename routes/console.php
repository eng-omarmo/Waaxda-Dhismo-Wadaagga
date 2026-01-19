<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Service;
use App\Models\Organization;
use App\Models\Project;
use App\Models\BusinessLicense;
use App\Models\BusinessLicenseDocument;
use App\Models\LandParcel;
use App\Models\ApartmentConstructionPermit;
use App\Models\OwnerProfile;
use App\Models\IdentityDocument;
use App\Models\Apartment;
use App\Models\Unit;
use App\Models\ServiceRequest;
use App\Models\PaymentVerification;
use App\Models\OwnershipHistory;
use App\Models\OwnershipClaim;
use App\Models\ApartmentTransfer;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('system:seed {action : seed|cleanup|reset} {--modules=*} {--entries=20} {--force} {--seed-env=dev}', function (string $action) {
    $faker = Faker::create();
    $tag = 'SEED';
    $entries = (int) $this->option('entries');
    $modules = (array) $this->option('modules');
    $force = (bool) $this->option('force');
    $env = (string) $this->option('seed-env');
    $all = empty($modules);
    $pick = function (array $choices) use ($faker) {
        return $choices[array_rand($choices)];
    };
    $existsSeed = function () use ($tag) {
        return Service::where('name', 'like', $tag . '%')->exists()
            || Organization::where('name', 'like', $tag . '%')->exists()
            || Project::where('registrant_name', 'like', $tag . '%')->exists()
            || BusinessLicense::where('company_name', 'like', $tag . '%')->exists()
            || LandParcel::where('verification_notes', 'like', $tag . '%')->exists()
            || OwnerProfile::where('full_name', 'like', $tag . '%')->exists()
            || Apartment::where('notes', 'like', $tag . '%')->exists()
            || Unit::where('unit_number', 'like', $tag . '%')->exists()
            || ServiceRequest::where('user_full_name', 'like', $tag . '%')->exists();
    };
    $cleanup = function () use ($tag) {
        $this->info('Cleaning seeded data');
        $this->output->progressStart(10);
        Service::where('name', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        BusinessLicenseDocument::whereIn('license_id', BusinessLicense::where('company_name', 'like', $tag . '%')->pluck('id'))->delete();
        BusinessLicense::where('company_name', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        Organization::where('name', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        Project::where('registrant_name', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        ApartmentConstructionPermit::where('applicant_name', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        LandParcel::where('verification_notes', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        IdentityDocument::whereIn('owner_profile_id', OwnerProfile::where('full_name', 'like', $tag . '%')->pluck('id'))->delete();
        $this->output->progressAdvance();
        Unit::where('unit_number', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        Apartment::where('notes', 'like', $tag . '%')->delete();
        $this->output->progressAdvance();
        PaymentVerification::whereIn('service_request_id', ServiceRequest::where('user_full_name', 'like', $tag.'%')->pluck('id'))->delete();
        $this->output->progressAdvance();
        OwnershipClaim::where('claimant_name', 'like', $tag.'%')->delete();
        $this->output->progressAdvance();
        OwnershipHistory::where('transfer_reference_number', 'like', $tag.'%')->delete();
        $this->output->progressAdvance();
        ApartmentTransfer::where('transfer_reference_number', 'like', $tag.'%')->delete();
        $this->output->progressAdvance();
        ServiceRequest::where('user_full_name', 'like', $tag . '%')->delete();
        $this->output->progressFinish();
    };
    if ($action === 'cleanup') {
        $cleanup();
        return;
    }
    if ($action === 'reset') {
        $cleanup();
        $action = 'seed';
    }
    if ($action !== 'seed') {
        $this->error('Invalid action');
        return;
    }
    if ($existsSeed() && ! $force) {
        $this->warn('Seed data already exists. Use --force or run cleanup/reset.');
        return;
    }
    $this->info('Seeding start');
    $this->info('Environment: ' . $env);
    $this->info('Entries per module: ' . $entries);
    $seedServices = function () use ($tag) {
        $this->info('Seeding services');
        $mapped = [
            'Construction Permit Application' => 'construction-permit-application',
            'Developer Registration' => 'developer-registration',
            'Project Registration' => 'project-registration',
            'Business License Processing' => 'business-license',
            'Property Transfer Services' => 'property-transfer-services',
            'Ownership Certificate' => 'ownership-certificate',
        ];
        foreach ($mapped as $name => $slug) {
            Service::updateOrCreate(['slug' => $slug], [
                'name' => $tag . ' ' . $name,
                'description' => 'Seeded ' . $name,
                'price' => rand(100, 400),
                'icon_color' => 'bg-primary',
                'icon_class' => 'bi-gear',
                'slug' => $slug,
            ]);
        }
    };
    $seedOrganizations = function (int $count) use ($faker, $tag, $pick) {
        $this->info('Seeding organizations');
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            Organization::create([
                'name' => $tag . ' ' . $faker->company,
                'registration_number' => strtoupper($faker->bothify('REG-#####')),
                'address' => $faker->streetAddress,
                'type' => $pick(['Developer', 'Contractor', 'Consultant', 'Other']),
                'contact_full_name' => $faker->name,
                'contact_role' => $pick(['Owner', 'Director', 'Manager']),
                'contact_phone' => '061' . rand(1000000, 9999999),
                'contact_email' => $faker->safeEmail,
                'status' => 'pending',
                'admin_notes' => $tag,
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedProjects = function (int $count) use ($faker, $tag) {
        $this->info('Seeding projects');
        $orgIds = Organization::pluck('id')->all();
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            Project::create([
                'project_name' => $faker->catchPhrase,
                'location_text' => $faker->city,
                'developer_id' => $orgIds ? $orgIds[array_rand($orgIds)] : null,
                'status' => $i % 2 === 0 ? 'Draft' : 'Submitted',
                'registrant_name' => $tag . ' ' . $faker->name,
                'registrant_phone' => '061' . rand(1000000, 9999999),
                'registrant_email' => $faker->safeEmail,
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedLandParcels = function (int $count) use ($faker, $tag, $pick) {
        $this->info('Seeding land parcels');
        $districts = ['Hodan', 'Wadajir', 'Bondhere', 'Yaqshid', 'Karan', 'Shibis'];
        $types = ['Private', 'Shared', 'Government', 'Leased'];
        $statuses = ['Unverified', 'PendingVerification', 'Verified', 'Rejected'];
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            LandParcel::create([
                'plot_number' => 'SEED-PLT-' . strtoupper(Str::random(6)),
                'title_number' => 'T-' . strtoupper(Str::random(5)),
                'location_district' => $pick($districts),
                'location_region' => 'Banaadir',
                'size_sqm' => rand(200, 1200),
                'current_owner_name' => $faker->name,
                'current_owner_national_id' => 'SOM-' . rand(100000, 999999),
                'ownership_type' => $pick($types),
                'verification_status' => $pick($statuses),
                'verification_documents_path' => [],
                'verification_notes' => $tag,
                'last_verification_date' => now()->subDays(rand(1, 365))->toDateString(),
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedPermits = function (int $count) use ($faker, $tag, $pick) {
        $this->info('Seeding construction permits');
        $plots = LandParcel::pluck('plot_number')->all();
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            $plot = $plots ? $plots[array_rand($plots)] : 'SEED-PLT-' . strtoupper(Str::random(6));
            ApartmentConstructionPermit::create([
                'applicant_name' => $tag . ' ' . $faker->name,
                'national_id_or_company_registration' => 'SOM-' . rand(100000, 999999),
                'land_plot_number' => $plot,
                'location' => $faker->city,
                'number_of_floors' => rand(1, 10),
                'number_of_units' => rand(1, 50),
                'engineer_or_architect_name' => $faker->name,
                'engineer_or_architect_license' => 'LIC-' . strtoupper(Str::random(8)),
                'permit_status' => $pick(['Pending', 'Approved', 'Rejected']),
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedOwnerProfiles = function (int $count) use ($faker, $tag, $pick) {
        $this->info('Seeding owner profiles, apartments, units');
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            $owner = OwnerProfile::create([
                'full_name' => $tag . ' ' . $faker->name,
                'national_id' => 'SOM-' . rand(100000, 999999),
                'tax_id_number' => 'TIN-' . strtoupper(Str::random(8)),
                'contact_phone' => '061' . rand(1000000, 9999999),
                'contact_email' => $faker->safeEmail,
                'address_text' => $faker->streetAddress,
            ]);
            if (Schema::hasTable('identity_documents')) {
                IdentityDocument::create([
                    'owner_profile_id' => $owner->id,
                    'document_type' => 'NationalID',
                    'document_number' => 'ID-' . strtoupper(Str::random(10)),
                    'file_path' => 'seed/identity/' . $owner->id . '.pdf',
                    'verified_at' => now()->subDays(rand(1, 100)),
                    'version' => 1,
                ]);
            }
            $apt = Apartment::create([
                'name' => $tag . ' ' . $faker->company . ' Apartments',
                'address_city' => $faker->city,
                'contact_name' => $faker->name,
                'contact_phone' => '061' . rand(1000000, 9999999),
                'contact_email' => $faker->safeEmail,
                'notes' => $tag,
                'owner_profile_id' => $owner->id,
            ]);
            $unitsCount = rand(2, 8);
            for ($u = 1; $u <= $unitsCount; $u++) {
                Unit::create([
                    'apartment_id' => $apt->id,
                    'unit_number' => $tag . '-U' . str_pad((string) $u, 3, '0', STR_PAD_LEFT),
                    'unit_type' => $pick(['Studio', '1BR', '2BR', '3BR']),
                    'square_footage' => rand(350, 1800),
                    'monthly_rent' => rand(100, 1200),
                    'status' => $pick(['vacant', 'occupied', 'maintenance']),
                ]);
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedLicenses = function (int $count) use ($faker, $tag, $pick) {
        $this->info('Seeding business licenses');
        $projects = Project::pluck('id')->all();
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            $lic = BusinessLicense::create([
                'company_name' => $tag . ' ' . $faker->company,
                'project_id' => $projects ? $projects[array_rand($projects)] : null,
                'license_type' => $pick(['Rental', 'Commercial']),
                'status' => 'pending',
                'verification_status' => 'unverified',
                'expires_at' => now()->addYear(),
                'registrant_name' => $faker->name,
                'registrant_email' => $faker->safeEmail,
                'registrant_phone' => '061' . rand(1000000, 9999999),
                'admin_comments' => $tag,
            ]);
            BusinessLicenseDocument::create([
                'license_id' => $lic->id,
                'file_name' => 'seed_doc.pdf',
                'file_path' => 'seed/license_docs/' . $lic->id . '.pdf',
                'file_type' => 'pdf',
                'document_label' => 'Seed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedServiceRequests = function (int $count) use ($faker, $tag) {
        $this->info('Seeding service requests');
        $serviceIds = Service::pluck('id')->all();
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            ServiceRequest::create([
                'service_id' => $serviceIds ? $serviceIds[array_rand($serviceIds)] : null,
                'user_id' => null,
                'user_full_name' => $tag . ' ' . $faker->name,
                'user_email' => $faker->safeEmail,
                'user_phone' => '061' . rand(1000000, 9999999),
                'user_national_id' => 'SOM-' . rand(100000, 999999),
                'request_details' => ['source' => 'seed', 'env' => $env],
                'status' => 'pending',
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedPaymentVerifications = function (int $count) use ($faker) {
        $this->info('Seeding payment verifications');
        $requests = ServiceRequest::pluck('id')->all();
        $admins = User::where('role', 'admin')->pluck('id')->all();
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            PaymentVerification::create([
                'service_request_id' => $requests ? $requests[array_rand($requests)] : null,
                'amount' => rand(50, 500),
                'payment_date' => now()->subDays(rand(1, 60))->toDateString(),
                'reference_number' => strtoupper($faker->bothify('PAY-########')),
                'verified_by' => $admins ? $admins[array_rand($admins)] : null,
                'verified_at' => now()->subDays(rand(1, 30)),
                'status' => $faker->randomElement(['pending','verified','rejected']),
                'notes' => 'seed',
                'reconciled_amount' => rand(50, 500),
                'reconciliation_notes' => 'seed',
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedOwnership = function (int $count) use ($faker, $tag) {
        $this->info('Seeding ownership claims and histories');
        $units = Unit::pluck('id')->all();
        $apartments = Apartment::pluck('id')->all();
        $owners = OwnerProfile::pluck('id')->all();
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            if ($units) {
                OwnershipClaim::create([
                    'unit_id' => $units[array_rand($units)],
                    'claimant_name' => $tag.' '.$faker->name,
                    'claimant_national_id' => 'SOM-'.rand(100000, 999999),
                    'claimant_phone' => '061'.rand(1000000, 9999999),
                    'claimant_email' => $faker->safeEmail,
                    'evidence_documents' => ['seed' => true],
                    'status' => $faker->randomElement(['pending','approved','rejected']),
                    'reviewer_comments' => 'seed',
                ]);
            }
            if ($apartments && $owners) {
                OwnershipHistory::create([
                    'apartment_id' => $apartments[array_rand($apartments)],
                    'owner_profile_id' => $owners[array_rand($owners)],
                    'started_at' => now()->subDays(rand(100, 500))->toDateString(),
                    'ended_at' => null,
                    'transfer_reference_number' => 'SEED-TRF-'.strtoupper(Str::random(8)),
                    'recorded_by_admin_id' => null,
                ]);
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedTransfers = function (int $count) use ($faker, $tag) {
        $this->info('Seeding apartment transfers');
        $apartments = Apartment::pluck('id')->all();
        $owners = OwnerProfile::pluck('id')->all();
        $this->output->progressStart($count);
        for ($i = 0; $i < $count; $i++) {
            ApartmentTransfer::create([
                'transfer_reference_number' => 'SEED-TRF-'.strtoupper(Str::random(10)),
                'apartment_number' => $apartments ? $apartments[array_rand($apartments)] : null,
                'unit_number' => 'U'.str_pad((string) rand(1, 50), 3, '0', STR_PAD_LEFT),
                'previous_owner_name' => $tag.' '.$faker->name,
                'previous_owner_id' => 'SOM-'.rand(100000, 999999),
                'new_owner_name' => $tag.' '.$faker->name,
                'new_owner_id' => 'SOM-'.rand(100000, 999999),
                'transfer_reason' => 'seed',
                'transfer_date' => now()->subDays(rand(10, 120))->toDateString(),
                'approval_status' => $faker->randomElement(['Pending','Approved','Rejected']),
            ]);
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    };
    $seedUsers = function () use ($faker) {
        $this->info('Seeding users');
        if (! User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'email' => 'admin@example.com',
                'password' => 'password',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'contact_phone' => '0610000000',
                'role' => 'admin',
                'active' => true,
            ]);
        }
        if (! User::where('email', 'user@example.com')->exists()) {
            User::create([
                'email' => 'user@example.com',
                'password' => 'password',
                'first_name' => 'Test',
                'last_name' => 'User',
                'contact_phone' => '0610000001',
                'role' => 'user',
                'active' => true,
            ]);
        }
    };
    $run = function (string $name, callable $fn) use ($modules, $all) {
        if ($all || in_array($name, $modules)) {
            $fn();
        } else {
            $this->line('Skipping ' . $name);
        }
    };
    $runCount = function (string $name, callable $fn) use ($modules, $all, $entries) {
        if ($all || in_array($name, $modules)) {
            $fn($entries);
        } else {
            $this->line('Skipping ' . $name);
        }
    };
    $run('users', $seedUsers);
    $run('services', $seedServices);
    $runCount('organizations', $seedOrganizations);
    $runCount('projects', $seedProjects);
    $runCount('land', $seedLandParcels);
    $runCount('permits', $seedPermits);
    $runCount('owners', $seedOwnerProfiles);
    $runCount('licenses', $seedLicenses);
    $runCount('requests', $seedServiceRequests);
    $runCount('payments', $seedPaymentVerifications);
    $runCount('ownership', $seedOwnership);
    $runCount('transfers', $seedTransfers);
    $this->info('Seeding complete');
})->purpose('Seed or cleanup comprehensive system data with options');
