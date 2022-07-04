<?php

namespace App\Helpers;

use App\Models\Account;

class AccountHelper
{
    /**
     * Generate the Account short code.
     * The short code has to be unique in the list of accounts.
     *
     * @return string
     */
    public static function generateShortCode(): string
    {
        $isUnique = false;
        while ($isUnique === false) {
            $shortCode = self::generateUniqueId();

            $found = Account::where('shortcode', $shortCode)->first();
            if ($found === null) {
                $isUnique = true;
            }
        }

        return $shortCode;
    }

    public static function generateUniqueId(int $length = 8): string
    {
        return substr(md5(uniqid(mt_rand(), true)), 0, $length);
    }
}
