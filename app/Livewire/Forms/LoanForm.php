<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class LoanForm extends Form
{
    #[Validate('required|exists:books,id')]
    public $book_id;

    #[Validate('required|exists:users,id')]
    public $user_id;

    #[Validate('required|date')]
    public $borrowed_at;

    #[Validate('nullable|date')]
    public $returned_at;

    #[Validate('required|date')]
    public $due_date;
}
