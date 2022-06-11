<?php

namespace App\Contact\ManageFamily\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Family;
use App\Services\BaseService;

class DestroyFamily extends BaseService implements ServiceInterface
{
    private array $data;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'author_id' => 'required|integer|exists:users,id',
            'family_id' => 'required|integer|exists:families,id',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     *
     * @return array
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'vault_must_belong_to_account',
            'author_must_be_vault_editor',
        ];
    }

    /**
     * Destroy a family.
     *
     * @param  array  $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $family = Family::where('vault_id', $data['vault_id'])
            ->findOrFail($data['family_id']);

        $family->delete();
    }
}
