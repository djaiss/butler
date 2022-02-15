<?php

namespace Tests\Unit\Controllers\Vault\Contact\Modules\ImportantDates\ViewHelpers;

use Tests\TestCase;
use App\Models\Gender;
use App\Models\Contact;
use App\Models\Pronoun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Controllers\Vault\Contact\Modules\ImportantDates\ViewHelpers\ModuleImportantDatesViewHelper;
use App\Models\ContactDate;
use App\Models\User;

class ModuleImportantDatesViewHelperTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_gets_the_data_needed_for_the_view(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $date = ContactDate::factory()->create([
            'contact_id' => $contact->id,
            'date' => 1981,
        ]);

        $array = ModuleImportantDatesViewHelper::data($contact, $user);

        $this->assertEquals(
            2,
            count($array)
        );

        $this->assertArrayHasKey('dates', $array);
        $this->assertArrayHasKey('url', $array);

        $this->assertEquals(
            [
                0 => [
                    'id' => $date->id,
                    'label' => $date->label,
                    'date' => '1981',
                    'type' => ContactDate::TYPE_BIRTHDATE,
                    'age' => 41,
                ],
            ],
            $array['dates']->toArray()
        );
    }
}