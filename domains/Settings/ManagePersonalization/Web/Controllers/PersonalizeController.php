<?php

namespace App\Settings\ManagePersonalization\Web\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use App\Settings\ManagePersonalization\Web\ViewHelpers\PersonalizeIndexViewHelper;

class PersonalizeController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeIndexViewHelper::data(),
        ]);
    }
}
