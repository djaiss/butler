<?php

namespace App\Contact\ManageTasks\Services;

use App\Models\ContactTask;
use Carbon\Carbon;
use App\Jobs\CreateAuditLog;
use App\Services\BaseService;
use App\Jobs\CreateContactLog;
use App\Models\ContactReminder;
use App\Interfaces\ServiceInterface;

class CreateContactTask extends BaseService implements ServiceInterface
{
    private ContactTask $task;
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
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',

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
     * Create a contact task.
     *
     * @param  array  $data
     * @return ContactTask
     */
    public function execute(array $data): ContactTask
    {
        $this->validateRules($data);
        $this->data = $data;

        $this->createContactTask();
        $this->updateLastEditedDate();

        return $this->task;
    }

    private function createContactTask(): void
    {
        $this->task = ContactTask::create([
            'contact_id' => $this->data['contact_id'],
            'label' => $this->data['label'],
            'description' => $this->valueOrNull($this->data, 'description'),
        ]);
    }

    private function updateLastEditedDate(): void
    {
        $this->contact->last_updated_at = Carbon::now();
        $this->contact->save();
    }
}
