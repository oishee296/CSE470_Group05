<?php

namespace Database\Seeders;

use App\Models\VolunteerProfile;
use Illuminate\Database\Seeder;

class VolunteerSeeder extends Seeder
{
    /**
     * Seed sample volunteer records for demonstration (R17).
     */
    public function run(): void
    {
        $volunteers = [
            [
                'full_name' => 'Ayesha Rahman',
                'email' => 'ayesha.rahman@example.com',
                'phone_number' => '01710000001',
                'address' => 'Feni Sadar, Feni',
                'skills' => 'Medical',
                'certification_name' => 'Basic First Aid Certificate',
                'certification_status' => 'Verified',
                'registration_status' => 'Approved',
            ],
            [
                'full_name' => 'Mahmud Hasan',
                'email' => 'mahmud.hasan@example.com',
                'phone_number' => '01710000002',
                'address' => 'Noakhali Sadar, Noakhali',
                'skills' => 'Rescue',
                'certification_name' => 'Swift Water Rescue Training',
                'certification_status' => 'Verified',
                'registration_status' => 'Approved',
            ],
            [
                'full_name' => 'Nusrat Jahan',
                'email' => 'nusrat.jahan@example.com',
                'phone_number' => '01710000003',
                'address' => 'Cumilla Sadar, Cumilla',
                'skills' => 'Logistics',
                'certification_name' => 'Relief Logistics Workshop',
                'certification_status' => 'Pending',
                'registration_status' => 'Pending',
            ],
            [
                'full_name' => 'Rakibul Islam',
                'email' => 'rakibul.islam@example.com',
                'phone_number' => '01710000004',
                'address' => 'Lakshmipur Sadar, Lakshmipur',
                'skills' => 'General Assistance',
                'certification_name' => null,
                'certification_status' => 'Pending',
                'registration_status' => 'Pending',
            ],
            [
                'full_name' => 'Farzana Akter',
                'email' => 'farzana.akter@example.com',
                'phone_number' => '01710000005',
                'address' => 'Feni, Bangladesh',
                'skills' => 'Medical',
                'certification_name' => 'Nursing Assistant Certificate',
                'certification_status' => 'Rejected',
                'registration_status' => 'Rejected',
            ],
        ];

        foreach ($volunteers as $data) {
            VolunteerProfile::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}
