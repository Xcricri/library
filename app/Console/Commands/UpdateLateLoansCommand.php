<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:update-late-loans-command')]
#[Description('Command description')]
class UpdateLateLoansCommand extends Command
{
    protected $signature = 'app:update-late-loans';

    protected $description = 'Mengubah status peminjaman menjadi late jika melewati due_date dan belum dikembalikan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Cari pinjaman yang:
        // 1. Statusnya masih 'borrowed'
        // 2. Belum dikembalikan (returned_at IS NULL)
        // 3. Tanggal due_date sudah kurang dari hari ini (< now)
        $affectedRows = Loan::where('status', 'borrowed')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'late']);

        $this->info("Berhasil memperbarui {$affectedRows} data peminjaman menjadi terlambat (late).");
    }
}
