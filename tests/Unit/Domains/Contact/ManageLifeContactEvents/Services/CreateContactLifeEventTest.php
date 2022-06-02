<?php

namespace Tests\Unit\Domains\Contact\ManageLifeContactEvents\Services;

use App\Contact\ManageLifeContactEvents\Services\CreateContactLifeEvent;
use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\Contact;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateContactLifeEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_life_event(): void
    {
        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $lifeEventCategory = LifeEventCategory::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $lifeEventType = LifeEventType::factory()->create([
            'life_event_category_id' => $lifeEventCategory->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new CreateContactLifeEvent)->execute($request);
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
        $lifeEventCategory = LifeEventCategory::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $lifeEventType = LifeEventType::factory()->create([
            'life_event_category_id' => $lifeEventCategory->id,
        ]);

        $this->executeService($regis, $account, $vault, $contact, $lifeEventType);
    }

    /** @test */
    public function it_fails_if_contact_doesnt_belong_to_vault(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_EDIT, $vault);
        $contact = Contact::factory()->create();
        $lifeEventCategory = LifeEventCategory::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $lifeEventType = LifeEventType::factory()->create([
            'life_event_category_id' => $lifeEventCategory->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_vault(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_VIEW, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $lifeEventCategory = LifeEventCategory::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $lifeEventType = LifeEventType::factory()->create([
            'life_event_category_id' => $lifeEventCategory->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType);
    }

    private function executeService(User $author, Account $account, Vault $vault, Contact $contact, LifeEventType $lifeEventType): void
    {
        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'life_event_type_id' => $lifeEventType->id,
            'contact_id' => $contact->id,
            'summary' => 'super title',
            'started_at' => '1990-01-01',
            'ended_at' => '1990-02-01',
        ];

        $lifeEvent = (new CreateContactLifeEvent)->execute($request);

        $this->assertDatabaseHas('contact_life_events', [
            'id' => $lifeEvent->id,
            'contact_id' => $contact->id,
            'life_event_type_id' => $lifeEventType->id,
            'summary' => 'super title',
            'started_at' => '1990-01-01 00:00:00',
            'ended_at' => '1990-02-01 00:00:00',
        ]);
    }
}
