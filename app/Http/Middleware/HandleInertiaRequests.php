<?php

namespace App\Http\Middleware;

use App\utils\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        if (Auth::user()) {
            $helpers = new helpers();
            return array_merge(parent::share($request), [
                'appName' => config('app.name'),
//            'auth' => [
                'user' => $request->user(),
//                    ->only('id', 'firstname', 'lastname', 'role', 'telefono', 'sucursal'),
//            ],
//            'ziggy' => function () {
//                return (new Ziggy)->toArray();
//            },
                'rolesP' => [
                    'admin' => [0, 1],
                    'vendor' => [0, 1, 2, 5],
                    'desing' => [0, 1, 2, 3, 4, 5],
                    'all' => [0, 1, 2, 3, 4, 5],
                ],
                'currency' => $helpers->Get_Currency_Code(),
                'day' => 1,
            ]);
        }
        return parent::share($request);
    }
}
