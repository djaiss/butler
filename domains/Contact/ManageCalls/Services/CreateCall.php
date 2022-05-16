<?php

namespace App\Contact\ManageCalls\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Call;
use App\Services\BaseService;
use Carbon\Carbon;

class CreateCall extends BaseService implements ServiceInterface
{
    private Call $call;
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
            'contact_id' => 'required|integer|exists:contacts,id',
            'called_at' => 'required|date_format:Y-m-d',
            'duration' => 'nullable|integer',
            'type' => 'required|string',
            'answered' => 'nullable|boolean',
            'who_initiated' => 'required|string',
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
            'contact_must_belong_to_vault',
        ];
    }

    /**
     * Create a call.
     *
     * @param  array  $data
     * @return Call
     */
    public function execute(array $data): Call
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->createCall();
        $this->updateLastEditedDate();

        return $this->call;
    }

    private function createCall(): void
    {
        $this->call = Call::create([
            'contact_id' => $this->data['contact_id'],
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'called_at' => $this->data['called_at'],
            'duration' => $this->valueOrNull($this->data, 'duration'),
            'type' => $this->data['type'],
            'answered' => $this->valueOrTrue($this->data, 'answered'),
            'who_initiated' => $this->data['who_initiated'],
        ]);
    }

    private function updateLastEditedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }
}
