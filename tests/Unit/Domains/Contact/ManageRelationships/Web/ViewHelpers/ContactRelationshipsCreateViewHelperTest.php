<?php

namespace Tests\Unit\Domains\Contact\ManageRelationships\Web\ViewHelpers;

use function env;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vault;
use App\Models\Gender;
use App\Models\Contact;
use App\Models\Pronoun;
use App\Models\RelationshipType;
use App\Models\RelationshipGroupType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Contact\ManageRelationships\Web\ViewHelpers\ContactRelationshipsCreateViewHelper;

class ContactRelationshipsCreateViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $user = User::factory()->create();
        $groupType = RelationshipGroupType::factory()->create([
            'account_id' => $user->account_id,
        ]);
        $type = RelationshipType::factory()->create([
            'relationship_group_type_id' => $groupType->id,
        ]);
        $vault = Vault::factory()->create([
            'account_id' => $user->account_id,
        ]);
        $gender = Gender::factory()->create([
            'account_id' => $vault->account_id,
        ]);
        $pronoun = Pronoun::factory()->create([
            'account_id' => $vault->account_id,
        ]);
        $contact = Contact::factory()->create([
            'vault_id' => $vault->id,
        ]);

        $array = ContactRelationshipsCreateViewHelper::data($vault);
        $this->assertEquals(
            4,
            count($array)
        );

        $this->assertArrayHasKey('genders', $array);
        $this->assertArrayHasKey('pronouns', $array);
        $this->assertArrayHasKey('relationship_types', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                0 => [
                    'id' => $gender->id,
                    'name' => $gender->name,
                ],
            ],
            $array['genders']->toArray()
        );

        $this->assertEquals(
            [
                0 => [
                    'id' => $pronoun->id,
                    'name' => $pronoun->name,
                ],
            ],
            $array['pronouns']->toArray()
        );

        $this->assertEquals(
            $groupType->id,
            $array['relationship_types']->toArray()[0]['id']
        );

        $this->assertEquals(
            [
                'store' => env('APP_URL').'/vaults/'.$vault->id.'/contacts',
                'back' => env('APP_URL').'/vaults/'.$vault->id.'/contacts',
            ],
            $array['url']
        );
    }
}
