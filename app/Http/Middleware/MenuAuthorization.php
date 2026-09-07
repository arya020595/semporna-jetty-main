<?php

namespace App\Http\Middleware;

use App\Actions\Menu\CheckMenu;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class MenuAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $menuCode = $request->segment(1);

        /**
         * @var User
         */
        $user = Auth::user();
        $roles = $user
            ? $user->roles->pluck('id')->toArray()
            : [];

        if (!(new CheckMenu())->execute($menuCode, $roles)) {
            abort(403);
        }

        //check user company
        if (
            $user->hasRole(User::ROLE_AGENT)
            && $menuCode != 'company-profile'
            && !optional($user->company->first())->is_completed
        ) {
            return redirect()->route("panel.company-profile.step1.edit")
                ->with("message", [
                    "status" => "warning",
                    "message" => "Fill your company data first!"
                ]);
        }

        App::setLocale("en");

        return $next($request);
    }
}
