<?php

namespace App\Http\Controllers\Vault;

use App\Features\Vault\ManageVault\ViewHelpers\VaultIndexViewHelper;
use Inertia\Inertia;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class VaultController extends Controller
{
    /**
     * Show all the vaults of the user.
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Vault/Index', [
            'user' => VaultIndexViewHelper::loggedUserInformation(),
        ]);
    }
}
