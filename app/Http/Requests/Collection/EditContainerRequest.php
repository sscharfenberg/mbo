<?php

namespace App\Http\Requests\Collection;

use App\Models\Container;
use Illuminate\Foundation\Http\FormRequest;

class EditContainerRequest extends FormRequest
{
    /**
     * The authenticated user must own the container.
     */
    public function authorize(): bool
    {
        $container = $this->route('container');

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
