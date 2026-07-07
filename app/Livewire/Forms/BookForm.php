<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class BookForm extends Form
{
    public $slug;

    #[Validate('required|string|min:3|max:255')]
    public $title;

    #[Validate('required|string|min:3|max:255')]
    public $author;

    #[Validate('required|string|min:3|max:255')]
    public $publisher_name;

    #[Validate(['nullable', 'image', 'max:2048'])]
    public $cover;

    #[Validate('nullable|string|size:13')]
    public $isbn;

    #[Validate('nullable|integer|min:0')]
    public $stock;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('nullable|date')]
    public $published_at;

    #[Validate(['genre_ids' => 'nullable|array', 'genre_ids.*' => 'exists:genres,id'])]
    public $genre_ids = [];

    #[Validate(['category_ids' => 'nullable|array', 'category_ids.*' => 'exists:categories,id'])]
    public $category_ids = [];
}
