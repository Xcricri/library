<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookBorrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BookBorrowingController extends Controller
{
    public function returnBook($borrowingId)
    {
        // Find the borrowing record
        $borrowing = BookBorrowing::findOrFail($borrowingId);

        // Find the associated book
        $book = Book::findOrFail($borrowing->book_id);

        // Calculate fine and update status
        $today = now()->startOfDay();
        $dueDate = Carbon::parse($borrowing->due_date)->startOfDay();

        // Set fine & status
        $fine = 0;
        $status = 'returned';

        // If today is after the due date
        $fine = $today->gt($dueDate)
            ? $today->diffInDays($dueDate) * 5000
            : 0;

        // Update borrowing record and book stock
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
