<?php

namespace App\Http\Requests\Collection;

use App\Models\Container;
use Illuminate\Foundation\Http\FormRequest;

class AddCardStackRequest extends FormRequest
{
    /**
     * When a container is provided, the authenticated user must own it.
     */
    public function authorize(): bool
    {
        $container = $this->route('container');

        if (! $container) {
            return true;
        }

        return $container instanceof Container && $container->user_id === $this->user()->id;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [];
    }
}
