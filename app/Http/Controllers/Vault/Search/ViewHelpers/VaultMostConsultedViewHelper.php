<?php

namespace App\Http\Controllers\Vault\Search\ViewHelpers;

use App\Models\Note;
use App\Models\Vault;
use App\Models\Contact;
use App\Helpers\DateHelper;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VaultMostConsultedViewHelper
{
    public static function data(Vault $vault, User $user): Collection
    {
        $records = DB::table('contact_vault_user')
            ->where('vault_id', $vault->id)
            ->where('user_id', $user->id)
            ->orderBy('number_of_views', 'desc')
            ->get()
            ->take(5)
            ->pluck('contact_id')
            ->toArray();

        $contactsCollection = collect();
        foreach ($records as $record) {
            $contact = Contact::find($record);

            $contactsCollection->push([
                'id' => $contact->id,
                'name' => $contact->getName($user)
            ]);
        }

        return $contactsCollection;
    }
}
