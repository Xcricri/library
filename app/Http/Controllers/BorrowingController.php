<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        $fine = $today->gt($dueDate)
            ? $today->diffInDays($dueDate) * 5000
            : 0;

        DB::transaction(function () use ($borrowing, $fine, $status, $book) {
            $borrowing->update([
                'returned_at' => now()->toDateString(),
                'fine' => $fine,
                'status' => $status
            ]);

            $book->increment('stock');
        });


        return redirect()->back()->with('message', 'Book has been returned.');
    }
}
