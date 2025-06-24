<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($id)
    {
        // Ambil status dan relasi akun + penyewaBot langsung
        $status = Status::with(['akun', 'penyewaBot'])->findOrFail($id);
        $penyewa = $status->penyewaBot;

        $waktu_beli = $penyewa?->waktu_beli
            ? \Carbon\Carbon::parse($penyewa->waktu_beli)->format('d M Y')
            : '-';

        $waktu_habis = $penyewa?->waktu_habis
            ? \Carbon\Carbon::parse($penyewa->waktu_habis)->format('d M Y')
            : '-';

        $html = '
        <html>
        <head>
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                    font-size: 12px;
                    color: #333;
                }
                .invoice-box {
                    max-width: 800px;
                    margin: auto;
                    padding: 30px;
                    border: 1px solid #eee;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                }
                .invoice-box h2 {
                    text-align: center;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    line-height: inherit;
                    text-align: left;
                    border-collapse: collapse;
                }
                table td {
                    padding: 5px;
                    vertical-align: top;
                }
                table tr.heading td {
                    background: #eee;
                    border-bottom: 1px solid #ddd;
                    font-weight: bold;
                }
                table tr.item td {
                    border-bottom: 1px solid #eee;
                }
                .total {
                    font-weight: bold;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 10px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="invoice-box">
                <h2>Invoice Langganan</h2>
                <table>
                    <tr class="heading">
                        <td>Informasi Pelanggan</td>
                        <td>Detail</td>
                    </tr>
                    <tr class="item">
                        <td>Nama</td>
                        <td>' . e($status->nama ?? '-') . '</td>
                    </tr>
                    <tr class="item">
                        <td>No WhatsApp</td>
                        <td>' . e($status->whatsapp_number ?? '-') . '</td>
                    </tr>
                    <tr class="item">
                        <td>Jenis Langganan</td>
                        <td>' . ucfirst(e($status->subscription_type)) . '</td>
                    </tr>
                    <tr class="item">
                        <td>Status Pembayaran</td>
                        <td>' . ucfirst(e($status->payment_status)) . '</td>
                    </tr>
                    <tr class="item">
                        <td>Masa Berlaku</td>
                        <td>' . $waktu_beli . ' s/d ' . $waktu_habis . '</td>
                    </tr>
                    <tr class="item total">
                        <td>Harga</td>
                        <td>Rp ' . number_format($status->price, 2, ',', '.') . '</td>
                    </tr>
                </table>

                <div class="footer">
                    <p>Invoice ini dibuat secara otomatis. Jika ada pertanyaan, hubungi admin melalui WhatsApp dengan nomor 081297355541.</p>
                </div>
            </div>
        </body>
        </html>
        ';

        $pdf = Pdf::loadHTML($html);
        return $pdf->download("invoice_{$status->id}.pdf");
    }
}