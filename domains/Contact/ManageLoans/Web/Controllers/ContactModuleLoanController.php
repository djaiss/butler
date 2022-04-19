<?php

namespace App\Contact\ManageLoans\Web\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contact\ManageLoans\Services\CreateLoan;
use App\Contact\ManageLoans\Services\UpdateLoan;
use App\Contact\ManageLoans\Services\DestroyLoan;
use App\Contact\ManageLoans\Web\ViewHelpers\ModuleLoanViewHelper;

class ContactModuleLoanController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        if ($request->input('amount_lent')) {
            $amount = $request->input('amount_lent') * 100;
        }

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'currency_id' => $request->input('currency_id'),
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'loaner_ids' => $request->input('loaners'),
            'loanee_ids' => $request->input('loanees'),
            'amount_lent' => $request->input('amount_lent') ? $amount : null,
            'loaned_at' => $request->input('loaned_at'),
        ];

        $loan = (new CreateLoan)->execute($data);

        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLoanViewHelper::dtoLoan($loan, $contact, Auth::user()),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $loanId)
    {
        if ($request->input('amount_lent')) {
            $amount = $request->input('amount_lent') * 100;
        }

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'loan_id' => $loanId,
            'currency_id' => $request->input('currency_id'),
            'type' => $request->input('type'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'loaner_ids' => $request->input('loaners'),
            'loanee_ids' => $request->input('loanees'),
            'amount_lent' => $request->input('amount_lent') ? $amount : null,
            'loaned_at' => $request->input('loaned_at'),
        ];

        $loan = (new UpdateLoan)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLoanViewHelper::dtoLoan($loan, $contact, Auth::user()),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $loanId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'loan_id' => $loanId,
        ];

        (new DestroyLoan)->execute($data);

        return response()->json([
            'data' => true,
        ], 200);
    }
}
