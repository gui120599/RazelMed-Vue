<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

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
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        /* return [
             ...parent::share($request),
             'name' => config('app.name'),
             'quote' => ['message' => trim($message), 'author' => trim($author)],
             'auth' => [
                 'user' => $request->user(),
             ],
             'ziggy' => [
                 ...(new Ziggy)->toArray(),
                 'location' => $request->url(),
             ],
             'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
         ];*/
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_super_admin' => $user->is_super_admin,
                    'prefer_institution_id' => $user->prefer_institution_id,
                    // Carrega as instituições que o usuário tem acesso via a relação many-to-many
                    'institutions' => $user->is_super_admin
                        ? \App\Models\Institution::all(['id', 'name'])->toArray() // Super Admin vê todas
                        : $user->institutions()->get(['id', 'name'])->toArray(), // Usuário comum vê as que ele está vinculado

                    // Carrega os dashboards que o usuário tem permissão granular
                    'accessible_dashboards' => $user->is_super_admin
                        ? \App\Models\Dashboard::all(['id', 'name', 'iframe_link', 'icon', 'institution_id'])->toArray() // Super Admin vê todos
                        : $user->accessibleDashboards()
                            ->whereIn('institution_id', $user->institutions->pluck('id')) // Opcional: filtra dashboards apenas de instituições que ele tem acesso
                            ->get(['id', 'name', 'iframe_link', 'icon', 'institution_id'])->toArray(),

                    'preferred_institution' => $user->prefer_institution_id
                        ? $user->preferredInstitution()->first(['id', 'name']) // Carrega a instituição preferencial
                        : null,
                ] : null,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ]);
    }
}
