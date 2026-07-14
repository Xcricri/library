<?php

use App\Models\User;

it('renders the public storage path for user avatars on the index page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $user = User::factory()->create([
        'avatar' => 'uw79MlllofBWWOKwudlwN9MMM9UqTkRPNBYsAP6Z.jpg',
    ]);

    $this->actingAs($admin);

    $response = $this->get(route('users.index'));

    $response->assertOk();
    $response->assertSee('/storage/avatars/'.$user->avatar, false);
});
