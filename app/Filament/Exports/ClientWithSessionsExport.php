<?php

namespace App\Filament\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;

class ClientWithSessionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $isAdmin;

    public function __construct()
    {
        $this->isAdmin = Auth::user()->role === 'admin';
    }

    /**
     * Ambil data klien beserta sesi-sesinya
     */
    public function collection()
    {
        $query = Client::with(['sessions.user']);

        // Filter sesuai role
        if (!$this->isAdmin) {
            $query->whereHas('sessions', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $clients = $query->get();
        
        $rows = collect();

        foreach ($clients as $client) {
            if ($client->sessions->isEmpty()) {
                // Jika klien belum punya sesi, tampilkan data klien saja
                $rows->push([
                    'client' => $client,
                    'session' => null,
                ]);
            } else {
                // Untuk setiap sesi, buat baris baru
                foreach ($client->sessions as $session) {
                    $rows->push([
                        'client' => $client,
                        'session' => $session,
                    ]);
                }
            }
        }

        return $rows;
    }

    /**
     * Header kolom Excel
     */
    public function headings(): array
    {
        $headers = [
            'Kode Klien',
            'Nama Klien',
            'Tanggal Lahir',
            'No. WhatsApp',
            'Alamat',
            'Diagnosis Awal',
            'Tanggal Sesi',
            'Jam Mulai',
            'Jam Selesai',
            'Status Sesi',
            'Psikolog',
            'Rekap/Hasil Sesi',
        ];

        // Tambah kolom khusus admin
        if ($this->isAdmin) {
            $headers[] = 'Tanggal Transfer';
            $headers[] = 'Status Pembayaran';
            $headers[] = 'Jumlah Bayar';
        }

        return $headers;
    }

    /**
     * Map data ke kolom Excel
     */
    public function map($row): array
    {
        $client = $row['client'];
        $session = $row['session'];

        $mapped = [
            $client->client_code,
            $client->name,
            $client->date_of_birth ? \Carbon\Carbon::parse($client->date_of_birth)->format('d/m/Y') : '',
            $client->whatsapp_number,
            $client->address,
            $client->initial_diagnosis,
            $session ? \Carbon\Carbon::parse($session->session_date)->format('d/m/Y') : '',
            $session ? \Carbon\Carbon::parse($session->session_start_time)->format('H:i') : '',
            $session ? \Carbon\Carbon::parse($session->session_end_time)->format('H:i') : '',
            $session ? ($session->session_status === 'terpakai' ? 'Terpakai' : 'Belum Terpakai') : '',
            $session ? $session->user->name : '',
            $session ? $session->session_description : '',
        ];

        // Tambah data khusus admin
        if ($this->isAdmin) {
            $mapped[] = $session && $session->transfer_date ? \Carbon\Carbon::parse($session->transfer_date)->format('d/m/Y') : '';
            $mapped[] = $session ? ($session->payment_status === 'lunas' ? 'Lunas' : 'DP') : '';
            $mapped[] = $session ? 'Rp ' . number_format($session->payment_amount, 0, ',', '.') : '';
        }

        return $mapped;
    }

    /**
     * Styling Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}