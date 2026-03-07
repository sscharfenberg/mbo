<?php

namespace App\Http\Middleware;

use App\Enums\Locale;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Laravel\Fortify\Features;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'csrfToken' => csrf_token(),
            'name' => config('app.name'),
            'auth' => [
                'user' => fn () => $request->user()
                    ? $request->user()->only('id', 'name', 'email')
                    : null
            ],
            'locale' => app()->getLocale(),
            'supportedLocales' => array_column(Locale::cases(), 'value'),
            'features' => [
                'registration' => Features::enabled(Features::registration()),
                'resetPasswords' => Features::enabled(Features::resetPasswords()),
                'emailVerification' => Features::enabled(Features::emailVerification()),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'type' => fn () => $request->session()->get('type'),
            ],
        ];
    }
}
