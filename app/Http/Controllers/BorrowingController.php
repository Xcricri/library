<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BorrowingController extends Controller
{
    public function returnBook($borrowingId)
    {
        $borrowing = Borrowing::findOrFail($borrowingId);

        $book = Book::findOrFail($borrowing->book_id);

        $today = now()->startOfDay();
        $dueDate = Carbon::parse($borrowing->due_date)->startOfDay();

        $fine = 0;
        $status = 'returned';

        // Jika tanggal melewati masa tenggat
        if ($today->gt($dueDate)) {
            $status = 'overdue';
            $daysLate = $today->diffInDays($today); // Hitung selisih hari
            $fine = $daysLate * 5000;
        }

        $borrowing->update([
            'returned_at' => now()->toDateString(),
            'fine' => $fine,
            'status' => $status
        ]);

        $book->increment('stock');

        return redirect()->back()->with('message', 'Book has been returned.');
    }
}
