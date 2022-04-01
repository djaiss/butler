<?php

namespace Tests\Unit\Services\Account\ManageCurrencies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Gender;
use App\Models\Account;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Currency;
use App\Services\Account\ManageCurrencies\ToggleCurrency;
use App\Services\Account\ManageGenders\CreateGender;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ToggleCurrencyTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_toggles_a_currency(): void
    {
        $ross = $this->createAdministrator();
        $this->executeService($ross, $ross->account);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new ToggleCurrency)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $this->executeService($ross, $account);
    }

    /** @test */
    public function it_fails_if_user_is_not_administrator(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $this->executeService($ross, $ross->account);
    }

    private function executeService(User $author, Account $account): void
    {
        $currency = Currency::factory()->create();

        $account->currencies()->attach($currency->id, ['active' => false]);

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'currency_id' => $currency->id,
        ];

        $gender = (new ToggleCurrency)->execute($request);

        $this->assertDatabaseHas('account_currencies', [
            'account_id' => $account->id,
            'currency_id' => $currency->id,
            'active' => true,
        ]);
    }
}