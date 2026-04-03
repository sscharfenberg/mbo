<?php

namespace App\Actions\Fortify;

use App\Enums\Locale;
use App\Models\User;
use App\Traits\PasswordValidationRules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
                    'max:'.User::NAME_MAX,
                    'min:'.User::NAME_MIN,
                    'unique:users',
                ],
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                    'unique:users',
                ],
                'password' => $this->passwordRules(),
                'password_confirmation' => ['same:password'],
                'locale' => ['required', Rule::enum(Locale::class)],
            ]);
        });

        $locale = Locale::from($input['locale']);

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'locale' => $locale,
            'currency' => $locale->defaultCurrency(),
        ]);
    }
}
