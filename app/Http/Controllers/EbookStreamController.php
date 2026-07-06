<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class EbookStreamController extends Controller
{
    public function view($slug)
    {
        $book = Book::whereSlug($slug)->firstOrFail();

        $path = 'ebooks/' . $book->ebook_file;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        $file = Storage::disk('public')->path($path);

        return response()->file($file, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($book->ebook_file) . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
