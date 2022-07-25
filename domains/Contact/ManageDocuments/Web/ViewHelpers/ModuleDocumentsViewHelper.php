<?php

namespace App\Contact\ManageDocuments\Events\Web\ViewHelpers;

use App\Helpers\FileHelper;
use App\Models\Contact;
use App\Models\File;

class ModuleDocumentsViewHelper
{
    public static function data(Contact $contact): array
    {
        $documentsCollection = $contact->files()
            ->where('type', File::TYPE_DOCUMENT)
            ->get()
            ->map(function (File $file) {
                return self::dto($file);
            });

        return [
            'documents' => $documentsCollection,
            'url' => [
                'edit' => route('contact.edit', [
                    'vault' => $contact->vault_id,
                    'contact' => $contact->id,
                ]),
            ],
        ];
    }

    public static function dto(File $file): array
    {
        return [
            'id' => $file->id,
            'url' => $file->cdn_url,
            'name' => $file->name,
            'mime_type' => $file->mime_type,
            'size' => FileHelper::getSize($file->size),
        ];
    }
}
