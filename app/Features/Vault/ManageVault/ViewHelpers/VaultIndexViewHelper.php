<?php

namespace App\Features\Vault\ManageVault\ViewHelpers;

use App\Models\Account;
use App\Models\Vault;
use Illuminate\Support\Facades\Auth;

class VaultIndexViewHelper
{
    /**
     * Get all the data needed for the general layout page.
     *
     * @param Vault $vault
     * @return array
     */
    public static function layoutData(): array
    {
        return [
            'user' => [
                'name' => Auth::user()->name,
            ],
            'url' => [
                'vaults' => route('vault.index'),
                'logout' => route('logout')
            ],
        ];
    }

    /**
     * Get all the data needed for the general layout page.
     *
     * @param Account $account
     * @return array
     */
    public static function data(Account $account): array
    {
        $vaults = Vault::where('account_id', $account->id)
            ->orderBy('name', 'asc')
            ->get();

        $vaultCollection = collect();
        foreach ($vaults as $vault) {
            $vaultCollection->push([
                'id' => $vault->id,
                'name' => $vault->name,
                'description' => $vault->description,
                'url' => [
                    'show' => route('vault.show', [
                        'vault' => $vault,
                    ]),
                ],
            ]);
        }

        return [
            'vaults' => $vaultCollection,
            'url' => [
                'vault' => [
                    'new' => route('vault.new'),
                ],
            ],
        ];
    }
}
