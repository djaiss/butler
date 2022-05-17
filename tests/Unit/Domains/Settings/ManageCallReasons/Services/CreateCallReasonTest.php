<?php

namespace Tests\Unit\Domains\Settings\ManageCallReasons\Services;

use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\CallReason;
use App\Models\CallReasonType;
use App\Models\User;
use App\Settings\ManageCallReasons\Services\CreateCallReason;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateCallReasonTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_call_reason(): void
    {
        $ross = $this->createAdministrator();
        $type = CallReasonType::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $ross->account, $type);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateCallReason)->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = $this->createAccount();
        $type = CallReasonType::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $account, $type);
    }

    /** @test */
    public function it_fails_if_user_is_not_administrator(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $type = CallReasonType::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $this->executeService($ross, $ross->account, $type);
    }

    private function executeService(User $author, Account $account, CallReasonType $type): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'call_reason_type_id' => $type->id,
            'label' => 'type name',
        ];

        $reason = (new CreateCallReason)->execute($request);

        $this->assertDatabaseHas('call_reasons', [
            'id' => $reason->id,
            'call_reason_type_id' => $type->id,
            'label' => 'type name',
        ]);

        $this->assertInstanceOf(
            CallReason::class,
            $reason
        );
    }
}
