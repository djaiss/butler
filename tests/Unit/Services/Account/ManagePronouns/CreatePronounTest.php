<?php

namespace Tests\Unit\Services\Account\ManagePronouns;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Models\Pronoun;
use App\Jobs\CreateAuditLog;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use App\Exceptions\NotEnoughPermissionException;
use App\Services\Account\ManagePronouns\CreatePronoun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreatePronounTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_pronoun(): void
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
        (new CreatePronoun)->execute($request);
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
        Queue::fake();

        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'name' => 'pronoun name',
        ];

        $pronoun = (new CreatePronoun)->execute($request);

        $this->assertDatabaseHas('pronouns', [
            'id' => $pronoun->id,
            'account_id' => $account->id,
            'name' => 'pronoun name',
        ]);

        $this->assertInstanceOf(
            Pronoun::class,
            $pronoun
        );

        Queue::assertPushed(CreateAuditLog::class, function ($job) {
            return $job->auditLog['action_name'] === 'pronoun_created';
        });
    }
}
