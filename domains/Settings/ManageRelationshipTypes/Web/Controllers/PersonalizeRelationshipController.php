<?php

namespace App\Settings\ManageRelationshipTypes\Web\Controllers;

use App\Http\Controllers\Controller;
use App\Settings\ManageRelationshipTypes\Services\CreateRelationshipGroupType;
use App\Settings\ManageRelationshipTypes\Services\DestroyRelationshipGroupType;
use App\Settings\ManageRelationshipTypes\Services\UpdateRelationshipGroupType;
use App\Settings\ManageRelationshipTypes\Web\ViewHelpers\PersonalizeRelationshipIndexViewHelper;
use App\Vault\ManageVault\Web\ViewHelpers\VaultIndexViewHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PersonalizeRelationshipController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Personalize/Relationships/Index', [
            'layoutData' => VaultIndexViewHelper::layoutData(),
            'data' => PersonalizeRelationshipIndexViewHelper::data(Auth::user()->account),
        ]);
    }

    public function store(Request $request)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'name' => $request->input('groupTypeName'),
            'can_be_deleted' => true,
        ];

        $groupType = (new CreateRelationshipGroupType())->execute($data);

        return response()->json([
            'data' => PersonalizeRelationshipIndexViewHelper::dtoGroupType($groupType),
        ], 201);
    }

    public function update(Request $request, int $groupTypeId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'relationship_group_type_id' => $groupTypeId,
            'name' => $request->input('groupTypeName'),
        ];

        $groupType = (new UpdateRelationshipGroupType())->execute($data);

        return response()->json([
            'data' => PersonalizeRelationshipIndexViewHelper::dtoGroupType($groupType),
        ], 200);
    }

    public function destroy(Request $request, int $groupTypeId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'relationship_group_type_id' => $groupTypeId,
        ];

        (new DestroyRelationshipGroupType())->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
