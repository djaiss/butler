<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Label;
use App\Models\Vault;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Couple;
use App\Models\Template;
use App\Models\ContactImportantDateType;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VaultTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_belongs_to_an_account()
    {
        $vault = Vault::factory()->create();
        $this->assertTrue($vault->account()->exists());
    }

    /** @test */
    public function it_belongs_to_a_template()
    {
        $template = Template::factory()->create();

        $vault = Vault::factory()->create([
            'default_template_id' => $template->id,
        ]);
        $this->assertTrue($vault->template()->exists());
    }

    /** @test */
    public function it_has_many_users(): void
    {
        $dwight = User::factory()->create();
        $vault = Vault::factory()->create([
            'account_id' => $dwight->account_id,
        ]);

        $vault->users()->sync([$dwight->id => ['permission' => Vault::PERMISSION_MANAGE]]);

        $this->assertTrue($vault->users()->exists());
    }

    /** @test */
    public function it_has_many_contacts(): void
    {
        $vault = Vault::factory()->create();
        $contact = Contact::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->contacts()->exists());
    }

    /** @test */
    public function it_has_many_labels(): void
    {
        $vault = Vault::factory()->create();
        $label = Label::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->labels()->exists());
    }

    /** @test */
    public function it_has_many_important_date_types(): void
    {
        $vault = Vault::factory()->create();
        $type = ContactImportantDateType::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->contactImportantDateTypes()->exists());
    }

    /** @test */
    public function it_has_many_companies(): void
    {
        $vault = Vault::factory()->create();
        Company::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->companies()->exists());
    }

    /** @test */
    public function it_has_many_couples(): void
    {
        $vault = Vault::factory()->create();
        Couple::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $this->assertTrue($vault->couples()->exists());
    }
}
