<?php

namespace App\Http\Middleware;

use App\Actions\Menu\BuildMenu;
use App\Actions\MyTask\GetCountMyTask;
use App\Actions\Notification\GetCountUnreadNotification;
use App\Actions\Notification\GetNotificationNavbar;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = Auth::user();
        $roles = [];
        if ($user) {
            $roles = $user->roles->pluck('id')->toArray();
        }

        $menus = (new BuildMenu())->execute($roles);
        $currentUrl = parse_url(url()->current(), PHP_URL_PATH);

        $currentUrl = explode("/", trim($currentUrl, "/"));
        $activeMenuCode = $currentUrl[0] ?? false;

        return array_merge(parent::share($request), [
            "menus" => $menus,
            "activeMenuCode" => $activeMenuCode,
            "appBaseUrl" => url('/'),
            "domain" => config('app.domain'),
            "lang" => App::getLocale(),
            "authUser" => $user ? (new UserResource($user))->toArray($request) : null,
            "flash" => [
                "message" => function () use ($request) {
                    return $request->session()->get('message');
                }
            ],
            'recaptchav2_sitekey' => config('recaptchav2.sitekey'),
        ]);
    }
}
