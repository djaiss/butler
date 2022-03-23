<?php

namespace Tests\Unit\Services\Contact\ManageLoan;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Vault;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\CreateAuditLog;
use App\Jobs\CreateContactLog;
use Illuminate\Support\Facades\Queue;
use App\Models\ContactInformationType;
use Illuminate\Validation\ValidationException;
use App\Services\Contact\ManageLoan\UpdateLoan;
use App\Exceptions\NotEnoughPermissionException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateLoanTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_loan(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $loaner = Contact::factory()->create(['vault_id' => $vault->id]);
        $loanee = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = Loan::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $loan, $loaner, $loanee);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateLoan)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $account = Account::factory()->create();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $loaner = Contact::factory()->create(['vault_id' => $vault->id]);
        $loanee = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = Loan::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $account, $vault, $contact, $loan, $loaner, $loanee);
    }

    /** @test */
    public function it_fails_if_contact_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create();
        $loaner = Contact::factory()->create(['vault_id' => $vault->id]);
        $loanee = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = Loan::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $loan, $loaner, $loanee);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_initial_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $loaner = Contact::factory()->create(['vault_id' => $vault->id]);
        $loanee = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = Loan::factory()->create([
            'contact_id' => $contact->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $loan, $loaner, $loanee);
    }

    /** @test */
    public function it_fails_if_loan_is_not_in_the_contact(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = ContactInformationType::factory()->create();
        $loaner = Contact::factory()->create(['vault_id' => $vault->id]);
        $loanee = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = Loan::factory()->create([]);

        $this->executeService($regis, $regis->account, $vault, $contact, $loan, $loaner, $loanee);
    }

    /** @test */
    public function it_fails_if_loaner_is_not_in_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = ContactInformationType::factory()->create();
        $loaner = Contact::factory()->create();
        $loanee = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = Loan::factory()->create([]);

        $this->executeService($regis, $regis->account, $vault, $contact, $loan, $loaner, $loanee);
    }

    /** @test */
    public function it_fails_if_loanee_is_not_in_the_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $loan = ContactInformationType::factory()->create();
        $loaner = Contact::factory()->create(['vault_id' => $vault->id]);
        $loanee = Contact::factory()->create();
        $loan = Loan::factory()->create([]);

        $this->executeService($regis, $regis->account, $vault, $contact, $loan, $loaner, $loanee);
    }

    private function executeService(User $author, Account $account, Vault $vault, Contact $contact, Loan $loan, Contact $loaner, Contact $loanee): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'contact_id' => $contact->id,
            'loan_id' => $loan->id,
            'type' => Loan::TYPE_DEBT,
            'name' => 'resto',
            'description' => 'This is incredible',
            'loaner_id' => $loaner->id,
            'loanee_id' => $loanee->id,
            'amount_lent' => 123,
            'loaned_at' => '2020-01-01',
        ];

        $loan = (new UpdateLoan)->execute($request);

        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'contact_id' => $contact->id,
            'type' => Loan::TYPE_DEBT,
            'name' => 'resto',
            'description' => 'This is incredible',
            'loaner_id' => $loaner->id,
            'loanee_id' => $loanee->id,
            'amount_lent' => 123,
            'loaned_at' => '2020-01-01 00:00:00',
        ]);

        $this->assertDatabaseHas('contact_feed_items', [
            'contact_id' => $contact->id,
            'feedable_id' => $loan->id,
            'feedable_type' => 'App\Models\Loan',
        ]);

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'loan_updated';
        });

        Queue::assertPushed(CreateContactLog::class, function ($job) {
            return $job->contactLog['action_name'] === 'loan_updated';
        });
    }
}
