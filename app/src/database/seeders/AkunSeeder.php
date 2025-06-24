<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Akun;
use Illuminate\Support\Facades\Hash;

class AkunSeeder extends Seeder
{
    public function run(): void
    {
        $akunList = [
            [
                'name' => 'Rivai',
                'email' => 'rivaimunthe02@gmail.com',
                'whatsapp_number' => '081212566015',
            ],
            [
                'name' => 'Ahmad Ramadhan',
                'email' => 'ahmad@example.com',
                'whatsapp_number' => '081234567890',
            ],
            [
                'name' => 'Sarah Intan',
                'email' => 'sarah@example.com',
                'whatsapp_number' => '089876543210',
            ],
        ];

        foreach ($akunList as $akun) {
            Akun::updateOrCreate(
                ['email' => $akun['email']],
                [
                    'name' => $akun['name'],
                    'whatsapp_number' => $akun['whatsapp_number'],
                    'password' => Hash::make('password'), // default password
                ]
            );
        }
    }
}
