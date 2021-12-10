<?php

namespace Tests\Unit\Services\Vault\ManageVault;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Services\Vault\ManageVault\CreateVault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateVaultTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_vault(): void
    {
        $ross = $this->createUser();
        $this->executeService($ross, $ross->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateVault)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createUser();
        $account = $this->createAccount();
        $this->executeService($ross, $account);
    }

    private function executeService(User $author, Account $account): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'name' => 'vault name',
            'type' => Vault::TYPE_PERSONAL,
        ];

        $vault = (new CreateVault)->execute($request);

        $this->assertDatabaseHas('vaults', [
            'id' => $vault->id,
            'account_id' => $account->id,
            'name' => 'vault name',
            'type' => Vault::TYPE_PERSONAL,
        ]);

        $this->assertDatabaseCount('contacts', 1);

        $contact = Contact::first();

        $this->assertFalse(
            $contact->can_be_deleted
        );

        $this->assertDatabaseHas('user_vault', [
            'vault_id' => $vault->id,
            'user_id' => $author->id,
            'contact_id' => $contact->id,
        ]);

        $this->assertInstanceOf(
            Vault::class,
            $vault
        );

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'vault_created';
        });
    }
}
