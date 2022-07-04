<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckAccountAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestedAccountShortCode = $request->route()->parameter('account');

        $account = Account::where('shortcode', $requestedAccountShortCode)->first();

        $exists = DB::table('users')->where('user_id', Auth::user()->id)
            ->where('account_id', $account->id)
            ->count() > 0;

        if ($exists) {
            return $next($request);
        } else {
            abort(401);
        }
    }
}
