<?php

namespace App\Http\Controllers\Settings\CancelAccount;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Account\ManageUsers\InviteUser;
use App\Http\Controllers\Vault\ViewHelpers\VaultIndexViewHelper;
use App\Http\Controllers\Settings\CancelAccount\ViewHelpers\CancelAccountIndexViewHelper;
use App\Services\Account\ManageAccount\DestroyAccount;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class CancelAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/CancelAccount/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => CancelAccountIndexViewHelper::data(),
        ]);
    }

    public function destroy(Request $request)
    {
        $hashedPassword = Hash::make($request->input('password'));
        if (Auth::user()->password != $hashedPassword) {
            throw new ModelNotFoundException('Passwords do not match.');
        }

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
        ];

        (new DestroyAccount)->execute($data);

        return response()->json([
            'data' => route('settings.user.index'),
        ], 200);
    }
}
