<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdateBook extends Form
{
    #[Validate('min:3|required|string|max:255')]
    public $title;

    #[Validate('min:3|required|string|max:255')]
    public $author;

    #[Validate('nullable|image|max:2048')]
    public $cover;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('nullable|date')]
    public $released_at;

    #[Validate('nullable|string')]
    public $review;

    #[Validate('nullable|array|exists:genres,id')]
    public $genre_ids = [];

    #[Validate('nullable|array|exists:categories,id')]
    public $category_ids = [];
}
