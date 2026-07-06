<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LoanController extends Controller
{
    public function returnBook($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        $book = Book::findOrFail($loan->book_id);

        $today = now()->startOfDay();
        $dueDate = Carbon::parse($loan->due_date)->startOfDay();

        $fine = 0;
        $status = 'returned';

        // Jika tanggal melewati masa tenggat
        if ($today->gt($dueDate)) {
            $status = 'overdue';
            $daysLate = $today->diffInDays($dueDate, false); // Hitung selisih hari
            $fine = $daysLate * 5000;
        }

        $loan->update([
            'returned_at' => now()->toDateString(),
            'fine' => $fine,
            'status' => $status
        ]);

        $book->increment('stock');

        return session()->flash('success', 'Buku telah dikembalikan');
    }
}
