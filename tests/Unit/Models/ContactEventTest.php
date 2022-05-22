<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactActivity;
use App\Models\ContactEvent;
use App\Models\Emotion;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ContactEventTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_contact()
    {
        $contactEvent = ContactEvent::factory()->create();

        $this->assertTrue($contactEvent->contact()->exists());
    }
}