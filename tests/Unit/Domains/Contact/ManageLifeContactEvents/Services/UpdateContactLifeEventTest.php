<?php

namespace Tests\Unit\Domains\Contact\ManageLifeContactEvents\Services;

use App\Contact\ManageLifeContactEvents\Services\CreateContactLifeEvent;
use App\Contact\ManageLifeContactEvents\Services\UpdateContactLifeEvent;
use App\Contact\ManageNotes\Services\CreateNote;
use App\Exceptions\NotEnoughPermissionException;
use App\Jobs\CreateAuditLog;
use App\Jobs\CreateContactLog;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ContactLifeEvent;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateContactLifeEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_life_event(): void
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
        $contactLifeEvent = ContactLifeEvent::factory()->create([
            'contact_id' => $contact->id,
            'life_event_type_id' => $lifeEventType->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType, $contactLifeEvent);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateContactLifeEvent)->execute($request);
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
        $contactLifeEvent = ContactLifeEvent::factory()->create([
            'contact_id' => $contact->id,
            'life_event_type_id' => $lifeEventType->id,
        ]);

        $this->executeService($regis, $account, $vault, $contact, $lifeEventType, $contactLifeEvent);
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
        $contactLifeEvent = ContactLifeEvent::factory()->create([
            'contact_id' => $contact->id,
            'life_event_type_id' => $lifeEventType->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType, $contactLifeEvent);
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
        $contactLifeEvent = ContactLifeEvent::factory()->create([
            'contact_id' => $contact->id,
            'life_event_type_id' => $lifeEventType->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType, $contactLifeEvent);
    }

    /** @test */
    public function it_fails_if_life_event_type_is_not_in_the_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_MANAGE, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $lifeEventCategory = LifeEventCategory::factory()->create();
        $lifeEventType = LifeEventType::factory()->create([
            'life_event_category_id' => $lifeEventCategory->id,
        ]);
        $contactLifeEvent = ContactLifeEvent::factory()->create([
            'contact_id' => $contact->id,
            'life_event_type_id' => $lifeEventType->id,
        ]);

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType, $contactLifeEvent);
    }

    /** @test */
    public function it_fails_if_contact_life_event_is_not_in_the_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $regis = $this->createUser();
        $vault = $this->createVault($regis->account);
        $vault = $this->setPermissionInVault($regis, Vault::PERMISSION_MANAGE, $vault);
        $contact = Contact::factory()->create(['vault_id' => $vault->id]);
        $lifeEventCategory = LifeEventCategory::factory()->create([
            'account_id' => $regis->account_id,
        ]);
        $lifeEventType = LifeEventType::factory()->create([
            'life_event_category_id' => $lifeEventCategory->id,
        ]);
        $contactLifeEvent = ContactLifeEvent::factory()->create();

        $this->executeService($regis, $regis->account, $vault, $contact, $lifeEventType, $contactLifeEvent);
    }

    private function executeService(User $author, Account $account, Vault $vault, Contact $contact, LifeEventType $lifeEventType, ContactLifeEvent $contactLifeEvent): void
    {
        $request = [
            'account_id' => $account->id,
            'vault_id' => $vault->id,
            'author_id' => $author->id,
            'life_event_type_id' => $lifeEventType->id,
            'contact_id' => $contact->id,
            'contact_life_event_id' => $contactLifeEvent->id,
            'summary' => 'super title',
            'started_at' => '1990-01-01',
            'ended_at' => '1990-02-01',
        ];

        $contactLifeEvent = (new UpdateContactLifeEvent)->execute($request);

        $this->assertDatabaseHas('contact_life_events', [
            'id' => $contactLifeEvent->id,
            'contact_id' => $contact->id,
            'life_event_type_id' => $lifeEventType->id,
            'summary' => 'super title',
            'started_at' => '1990-01-01 00:00:00',
            'ended_at' => '1990-02-01 00:00:00',
        ]);
    }
}
