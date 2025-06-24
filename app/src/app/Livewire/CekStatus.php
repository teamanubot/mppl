<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Status;
use App\Models\DataPenyewaBot;
use Illuminate\Support\Facades\Auth;

class CekStatus extends Component
{
    public $search;
    public $status;
    public $subscription;
    public $error;

    public function mount()
    {
        if (!Auth::guard('akun')->check()) {
            session()->flash('error', 'Silakan login terlebih dahulu untuk mengecek status.');
            return redirect()->route('akun.login');
        }
    }

    public function cek()
    {
        $this->reset(['status', 'subscription', 'error']);

        if (!$this->search) {
            $this->error = 'Harap isi ID atau nama untuk cek status.';
            return;
        }

        // Jika numeric, cari berdasarkan ID
        if (ctype_digit($this->search)) {
            $this->status = Status::find($this->search);

            if (!$this->status) {
                $this->error = "Data dengan ID {$this->search} tidak ditemukan.";
                return;
            }
        } else {
            // Cari berdasarkan kolom `name` di tabel statuses langsung
            $this->status = Status::where('nama', $this->search)->first();


            if (!$this->status) {
                $this->error = "Data dengan nama '{$this->search}' tidak ditemukan.";
                return;
            }
        }

        // Cek apakah status pembayaran disetujui
        if ($this->status->payment_status === 'approved') {
            $this->subscription = DataPenyewaBot::where('status_id', $this->status->id)
                ->where('jenisbot', $this->status->subscription_type)
                ->first();

            if (!$this->subscription) {
                $this->error = 'Data penyewa bot tidak ditemukan.';
            }
        } else {
            $this->error = 'Status pembayaran belum disetujui.';
        }
    }

    public function render()
    {
        return view('livewire.cek-status');
    }
}