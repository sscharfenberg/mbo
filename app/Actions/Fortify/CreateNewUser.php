<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Traits\PasswordValidationRules;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{

    use PasswordValidationRules;

    public function __construct(
        protected Request $request
    ) {}

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        precognitive(function () {
            $this->request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:'.config('mbo.db.user.name.max'),
                    'min:'.config('mbo.db.user.name.min'),
                    'unique:users'
                ],
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                ],
                'password' => $this->passwordRules(),
                'password_confirmation' => ['same:password'],
            ]);
        });

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
