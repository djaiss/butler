<?php

namespace Tests\Unit\Models;

use App\Models\Contact;
use App\Models\Currency;
use App\Models\Gift;
use App\Models\GiftOccasion;
use App\Models\GiftState;
use App\Models\GiftUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GiftTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_has_one_vault()
    {
        $gift = Gift::factory()->create();

        $this->assertTrue($gift->vault()->exists());
    }

    /** @test */
    public function it_has_one_occasion()
    {
        $giftOccasion = GiftOccasion::factory()->create();
        $gift = Gift::factory()->create([
            'gift_occasion_id' => $giftOccasion->id,
        ]);

        $this->assertTrue($gift->giftOccasion()->exists());
    }

    /** @test */
    public function it_has_one_state()
    {
        $giftState = GiftState::factory()->create();
        $gift = Gift::factory()->create([
            'gift_state_id' => $giftState->id,
        ]);

        $this->assertTrue($gift->giftState()->exists());
    }

    /** @test */
    public function it_has_one_currency()
    {
        $currency = Currency::factory()->create();
        $gift = Gift::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $this->assertTrue($gift->currency()->exists());
    }

    /** @test */
    public function it_has_many_contacts_as_donators(): void
    {
        $ross = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $gift = Gift::factory()->create();

        $gift->donators()->sync($ross->id);
        $gift->donators()->sync($monica->id);

        $this->assertTrue($gift->donators()->exists());
    }

    /** @test */
    public function it_has_many_contacts_as_recipients(): void
    {
        $ross = Contact::factory()->create([]);
        $monica = Contact::factory()->create();
        $gift = Gift::factory()->create();

        $gift->recipients()->sync($ross->id);
        $gift->recipients()->sync($monica->id);

        $this->assertTrue($gift->recipients()->exists());
    }

    /** @test */
    public function it_has_many_gift_urls(): void
    {
        $gift = Gift::factory()->create();
        GiftUrl::factory()->create([
            'gift_id' => $gift->id,
        ]);

        $this->assertTrue($gift->giftUrls()->exists());
    }
}
