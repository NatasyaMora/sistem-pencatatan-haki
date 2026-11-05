<?php

namespace App\Console\Commands;

use App\Models\Haki;
use Illuminate\Console\Command;

class CheckHakiExpiryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'haki:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa dan nonaktifkan data HAKI yang telah melewati tanggal kedaluwarsa';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $updated = Haki::syncExpiredStatuses();

        $this->info("Pemeriksaan masa berlaku HAKI selesai. Sebanyak {$updated} data diperbarui menjadi nonaktif.");

        return self::SUCCESS;
    }
}
