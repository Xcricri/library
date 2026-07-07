<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    #[Validate('min:5|required|string|max:255')]
    public $name;

    #[Validate('min:5|required|email|max:255')]
    public $email;

    #[Validate('nullable|image|max:2048')]
    public $avatar;

    #[Validate('nullable|in:admin,user')]
    public $role = '';

    #[Validate('nullable|min:8|confirmed')]
    public $password;

    public $password_confirmation = '';
}
