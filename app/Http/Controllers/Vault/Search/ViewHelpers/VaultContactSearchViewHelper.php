<?php

namespace App\Http\Controllers\Vault\Search\ViewHelpers;

use App\Models\Note;
use App\Models\Vault;
use App\Models\Contact;
use App\Helpers\DateHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class VaultContactSearchViewHelper
{
    public static function data(Vault $vault, string $term): Collection
    {
        $contacts = Contact::search($term)
            ->where('vault_id', $vault->id)
            ->take(5)
            ->get();

        $contactsCollection = $contacts->map(function (Contact $contact) {
            return [
                'id' => $contact->id,
                'name' => $contact->first_name . ' ' . $contact->last_name . ' ' . $contact->nickname . ' ' . $contact->maiden_name . ' ' . $contact->middle_name,
                'url' => route('contact.show', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ];
        });

        return $contactsCollection;
    }
}
