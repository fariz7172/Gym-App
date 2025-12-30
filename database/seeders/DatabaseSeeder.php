<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\MembershipPlan;
use App\Models\Member;
use App\Models\Membership;
use App\Models\Trainer;
use App\Models\GymClass;
use App\Models\ClassSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Branches
        $branches = [
            [
                'name' => 'GymPro Central',
                'code' => 'GPC',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'phone' => '021-12345678',
                'email' => 'central@gympro.id',
                'opening_time' => '06:00',
                'closing_time' => '22:00',
            ],
            [
                'name' => 'GymPro Senayan',
                'code' => 'GPS',
                'address' => 'Senayan City Mall Lt. 5, Jakarta Selatan',
                'phone' => '021-87654321',
                'email' => 'senayan@gympro.id',
                'opening_time' => '06:00',
                'closing_time' => '23:00',
            ],
            [
                'name' => 'GymPro Kelapa Gading',
                'code' => 'GPK',
                'address' => 'Mall of Indonesia Lt. 3, Jakarta Utara',
                'phone' => '021-11223344',
                'email' => 'kelapagading@gympro.id',
                'opening_time' => '05:30',
                'closing_time' => '22:00',
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::create($branchData);
        }

        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'branch_id' => null,
        ]);

        // Create Branch Admins
        $branchAdmins = [
            ['name' => 'Admin Central', 'email' => 'admin.central@gmail.com', 'branch_id' => 1],
            ['name' => 'Admin Senayan', 'email' => 'admin.senayan@gmail.com', 'branch_id' => 2],
            ['name' => 'Admin Kelapa Gading', 'email' => 'admin.kg@gmail.com', 'branch_id' => 3],
        ];

        foreach ($branchAdmins as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make('password'),
                'role' => 'branch_admin',
                'branch_id' => $admin['branch_id'],
            ]);
        }

        // Create Membership Plans
        $plans = [
            [
                'name' => 'Basic Monthly',
                'duration_days' => 30,
                'price' => 350000,
                'description' => 'Akses gym selama 30 hari',
                'features' => ['Akses gym', 'Loker gratis', 'Parking gratis'],
            ],
            [
                'name' => 'Premium 3 Bulan',
                'duration_days' => 90,
                'price' => 900000,
                'description' => 'Akses gym + kelas selama 3 bulan',
                'features' => ['Akses gym', 'Semua kelas', 'Loker gratis', 'Handuk gratis', '2x PT session'],
            ],
            [
                'name' => 'Gold 6 Bulan',
                'duration_days' => 180,
                'price' => 1600000,
                'description' => 'Paket lengkap 6 bulan',
                'features' => ['Akses gym', 'Semua kelas', 'Loker gratis', 'Handuk gratis', '5x PT session', 'Body composition analysis'],
            ],
            [
                'name' => 'Platinum Tahunan',
                'duration_days' => 365,
                'price' => 2800000,
                'description' => 'Paket premium 1 tahun penuh',
                'features' => ['Akses gym', 'Semua kelas', 'Loker premium', 'Handuk gratis', '12x PT session', 'Monthly body check', 'Priority booking'],
            ],
        ];

        foreach ($plans as $plan) {
            MembershipPlan::create($plan);
        }

        // Create Sample Trainers for Branch 1
        $trainers = [
            [
                'branch_id' => 1,
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'phone' => '081234567890',
                'gender' => 'male',
                'specialization' => 'Strength Training, Weight Loss',
                'bio' => 'Certified personal trainer dengan 5 tahun pengalaman',
                'hourly_rate' => 150000,
            ],
            [
                'branch_id' => 1,
                'name' => 'Sari Dewi',
                'email' => 'sari@gmail.com',
                'phone' => '081234567891',
                'gender' => 'female',
                'specialization' => 'Yoga, Pilates',
                'bio' => 'Instruktur yoga bersertifikat internasional',
                'hourly_rate' => 175000,
            ],
            [
                'branch_id' => 1,
                'name' => 'Andi Pratama',
                'email' => 'andi@gmail.com',
                'phone' => '081234567892',
                'gender' => 'male',
                'specialization' => 'Boxing, HIIT',
                'bio' => 'Former national boxing athlete',
                'hourly_rate' => 200000,
            ],
        ];

        foreach ($trainers as $trainer) {
            Trainer::create($trainer);
        }

        // Create Sample Classes for Branch 1
        $classes = [
            ['branch_id' => 1, 'name' => 'Yoga Flow', 'description' => 'Relaksasi dan fleksibilitas', 'duration_minutes' => 60, 'max_capacity' => 20],
            ['branch_id' => 1, 'name' => 'Zumba Party', 'description' => 'Cardio dance workout', 'duration_minutes' => 45, 'max_capacity' => 25],
            ['branch_id' => 1, 'name' => 'Boxing Fit', 'description' => 'Boxing untuk fitness', 'duration_minutes' => 60, 'max_capacity' => 15],
            ['branch_id' => 1, 'name' => 'HIIT Blast', 'description' => 'High intensity interval training', 'duration_minutes' => 30, 'max_capacity' => 20],
            ['branch_id' => 1, 'name' => 'Spinning', 'description' => 'Indoor cycling class', 'duration_minutes' => 45, 'max_capacity' => 20],
        ];

        foreach ($classes as $class) {
            $gymClass = GymClass::create($class);

            // Create schedules for each class
            $schedules = [
                ['day_of_week' => 'monday', 'start_time' => '07:00', 'end_time' => '08:00', 'trainer_id' => 1, 'room' => 'Studio A'],
                ['day_of_week' => 'wednesday', 'start_time' => '17:00', 'end_time' => '18:00', 'trainer_id' => 2, 'room' => 'Studio B'],
                ['day_of_week' => 'friday', 'start_time' => '18:00', 'end_time' => '19:00', 'trainer_id' => 1, 'room' => 'Studio A'],
            ];

            foreach ($schedules as $schedule) {
                ClassSchedule::create(array_merge($schedule, ['gym_class_id' => $gymClass->id]));
            }
        }

        // Create Sample Members
        $members = [
            [
                'branch_id' => 1,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '081111111111',
                'gender' => 'male',
                'date_of_birth' => '1990-05-15',
                'address' => 'Jl. Contoh No. 1, Jakarta',
                'status' => 'active',
            ],
            [
                'branch_id' => 1,
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '081222222222',
                'gender' => 'female',
                'date_of_birth' => '1992-08-20',
                'address' => 'Jl. Contoh No. 2, Jakarta',
                'status' => 'active',
            ],
            [
                'branch_id' => 1,
                'name' => 'Ahmad Rizky',
                'email' => 'ahmad@example.com',
                'phone' => '081333333333',
                'gender' => 'male',
                'date_of_birth' => '1988-12-10',
                'address' => 'Jl. Contoh No. 3, Jakarta',
                'status' => 'active',
            ],
        ];

        foreach ($members as $memberData) {
            $member = Member::create($memberData);

            // Create active membership
            Membership::create([
                'member_id' => $member->id,
                'membership_plan_id' => rand(1, 4),
                'start_date' => now()->subDays(rand(1, 30)),
                'end_date' => now()->addDays(rand(30, 180)),
                'status' => 'active',
            ]);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('- Super Admin: admin@gympro.id / password');
        $this->command->info('- Branch Admin: admin.central@gympro.id / password');
    }
}
