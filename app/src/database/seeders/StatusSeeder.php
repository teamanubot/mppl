<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;
use App\Models\Akun;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $sourcePath = public_path('front/images/BuktiTransfer.jpg');
        $destFilename = 'payment-proofs/' . Str::random(10) . '.jpg';
        Storage::disk('public')->put($destFilename, file_get_contents($sourcePath));

        $akunData = [
            'rivaimunthe02@gmail.com' => [
                'nama' => 'Rivai',
                'whatsapp_number' => '081212566015',
                'subscription_type' => 'official bot',
                'payment_status' => 'approved',
                'price' => 100000,
            ],
            'ahmad@example.com' => [
                'nama' => 'Ahmad Ramadhan',
                'whatsapp_number' => '081234567890',
                'subscription_type' => 'selfbot',
                'payment_status' => 'pending',
                'price' => 50000,
            ],
            'sarah@example.com' => [
                'nama' => 'Sarah Intan',
                'whatsapp_number' => '089876543210',
                'subscription_type' => 'official bot',
                'payment_status' => 'rejected',
                'price' => 0,
            ],
        ];

        foreach ($akunData as $email => $data) {
            $akun = Akun::where('email', $email)->first();
            if ($akun) {
                Status::create([
                    'akun_id' => $akun->id,
                    'nama' => $data['nama'],
                    'whatsapp_number' => $data['whatsapp_number'],
                    'subscription_type' => $data['subscription_type'],
                    'payment_status' => $data['payment_status'],
                    'payment_proof' => $data['payment_status'] === 'rejected' ? null : $destFilename,
                    'price' => $data['price'],
                ]);
            }
        }
    }
}
