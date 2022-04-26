<?php

namespace Tests\Unit\Domains\Vault\ManageCompanies\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Label;
use App\Models\Vault;
use App\Models\Account;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Company;
use App\Vault\ManageCompanies\Services\CreateCompany;
use App\Vault\ManageVaultSettings\Services\CreateLabel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateCompanyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_company(): void
    {
        $ross = $this->createAdministrator();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $this->executeService($ross, $ross->account, $vault);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateCompany)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_MANAGE, $vault);
        $this->executeService($ross, $account, $vault);
    }

    /** @test */
    public function it_fails_if_user_is_not_editor(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $vault = $this->createVault($ross->account);
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_VIEW, $vault);
        $this->executeService($ross, $ross->account, $vault);
    }

    /** @test */
    public function it_fails_if_vault_is_not_in_the_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $vault = $this->createVault(Account::factory()->create());
        $vault = $this->setPermissionInVault($ross, Vault::PERMISSION_VIEW, $vault);
        $this->executeService($ross, $ross->account, $vault);
    }

    private function executeService(User $author, Account $account, Vault $vault): void
    {
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'vault_id' => $vault->id,
            'name' => 'company name',
            'type' => Company::TYPE_ASSOCIATION,
        ];

        $company = (new CreateCompany)->execute($request);

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'vault_id' => $vault->id,
            'name' => 'company name',
            'type' => Company::TYPE_ASSOCIATION,
        ]);

        $this->assertInstanceOf(
            Company::class,
            $company
        );
    }
}
