<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateClientCodes extends Command
{
    /**
     * Nama dan signature dari perintah konsol.
     *
     * @var string
     */
    protected $signature = 'app:generate-client-codes';

    /**
     * Deskripsi dari perintah konsol.
     *
     * @var string
     */
    protected $description = 'Membuat kode unik untuk klien lama yang belum memilikinya';

    /**
     * Jalankan perintah konsol.
     */
    public function handle()
    {
        // 1. Cari semua klien yang kolom client_code-nya masih kosong (NULL)
        $clientsToUpdate = Client::whereNull('client_code')->get();

        if ($clientsToUpdate->isEmpty()) {
            $this->info('👍 Semua klien sudah memiliki kode. Tidak ada yang perlu dilakukan.');
            return 0;
        }

        $this->info("Menemukan {$clientsToUpdate->count()} klien yang perlu dibuatkan kode...");

        // Buat progress bar agar terlihat keren
        $bar = $this->output->createProgressBar($clientsToUpdate->count());
        $bar->start();

        // 2. Loop melalui setiap klien yang ditemukan
        foreach ($clientsToUpdate as $client) {
            do {
                // 3. Buat kode acak 6 karakter
                $code = Str::upper(Str::random(6));
            } while (Client::where('client_code', $code)->exists()); // Ulangi jika kode sudah ada

            // 4. Simpan kode baru ke database
            $client->client_code = $code;
            $client->save();

            $bar->advance(); // Majukan progress bar
        }

        $bar->finish();
        $this->newLine(2); // Beri spasi
        $this->info('✅ Selesai! Semua klien lama kini sudah memiliki kode unik.');

        return 0;
    }
}