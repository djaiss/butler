<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Label;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Vault\Contact\Modules\Label\ViewHelpers\ModuleLabelViewHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\Contact\ManageNote\UpdateNote;
use App\Services\Contact\ManageNote\DestroyNote;
use App\Services\Account\ManageLabels\CreateLabel;
use App\Http\Controllers\Vault\Contact\Modules\Note\ViewHelpers\ModuleNotesViewHelper;
use App\Services\Contact\AssignLabel\AssignLabel;

class ContactModuleLabelController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'name' => $request->input('name'),
            'description' => $request->input('body'),
            'bg_color' => $request->input('bg_color'),
            'text_color' => $request->input('text_color'),
        ];

        $note = (new CreateLabel)->execute($data);

        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleNotesViewHelper::dto($contact, $note, Auth::user()),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $labelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'label_id' => $labelId,
        ];

        $label = (new AssignLabel)->execute($data);

        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($label, $contact),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $noteId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'note_id' => $noteId,
        ];

        (new DestroyNote)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}