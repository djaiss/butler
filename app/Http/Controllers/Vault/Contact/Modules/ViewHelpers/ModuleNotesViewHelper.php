<?php

namespace App\Http\Controllers\Vault\Contact\Modules\ViewHelpers;

use App\Helpers\DateHelper;
use App\Models\Contact;
use App\Models\Note;
use App\Models\Vault;

class ModuleNotesViewHelper
{
    public static function data(Contact $contact): array
    {
        $notes = $contact->notes()->orderBy('created_at', 'desc')->take(3)->get();
        $notesCollection = $notes->map(function ($note) use ($contact) {
            return self::dto($contact, $note);
        });

        return [
            'notes' => $notesCollection,
            'url' => [
                'store' => route('contact.note.store', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
                'index' => route('contact.note.index', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(Contact $contact, Note $note): array
    {
        return [
            'id' => $note->id,
            'body' => $note->body,
            'title' => $note->title,
            'author' => $note->author ? $note->author->name : $note->author_name,
            'written_at' => DateHelper::formatDate($note->created_at),
            'url' => [
                'update' => route('contact.note.update', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'note' => $note->id,
                ]),
                'destroy' => route('contact.note.destroy', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                    'note' => $note->id,
                ]),
            ],
        ];
    }
}
