<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ViewHelpers\AcceptInvitationShowViewHelper;
use Inertia\Inertia;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\Account\ManageUsers\AcceptInvitation;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AcceptInvitationController extends Controller
{
    public function show(Request $request, string $code)
    {
        try {
            User::where('invitation_code', $code)
                ->whereNull('invitation_accepted_at')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return redirect('/');
        }

        return Inertia::render('Auth/AcceptInvitation', [
            'data' => AcceptInvitationShowViewHelper::data($code),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'invitation_code' => 'required|uuid',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'invitation_code' => $request->input('invitation_code'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'password' => $request->input('password'),
        ];

        $user = (new AcceptInvitation)->execute($data);

        Auth::login($user);

        return response()->json([
            'data' => route('vault.index'),
        ], 200);
    }
}
