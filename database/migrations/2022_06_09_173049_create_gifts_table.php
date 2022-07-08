<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // necessary for SQLlite
        Schema::enableForeignKeyConstraints();

        Schema::create('gift_occasions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('label');
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        Schema::create('gift_states', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('label');
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vault_id');
            $table->unsignedBigInteger('gift_occasion_id');
            $table->unsignedBigInteger('gift_state_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('estimated_price')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->datetime('received_at')->nullable();
            $table->datetime('given_at')->nullable();
            $table->datetime('bought_at')->nullable();
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
            $table->foreign('gift_occasion_id')->references('id')->on('gift_occasions')->onDelete('cascade');
            $table->foreign('gift_state_id')->references('id')->on('gift_states')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
        });

        Schema::create('gift_donators', function (Blueprint $table) {
            $table->unsignedBigInteger('gift_id');
            $table->unsignedBigInteger('contact_id');
            $table->timestamps();
            $table->foreign('gift_id')->references('id')->on('gifts')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::create('gift_recipients', function (Blueprint $table) {
            $table->unsignedBigInteger('gift_id');
            $table->unsignedBigInteger('contact_id');
            $table->timestamps();
            $table->foreign('gift_id')->references('id')->on('gifts')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::create('gift_urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gift_id');
            $table->string('url');
            $table->timestamps();
            $table->foreign('gift_id')->references('id')->on('gifts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gift_occasions');
        Schema::dropIfExists('gift_states');
        Schema::dropIfExists('gifts');
        Schema::dropIfExists('gift_donators');
        Schema::dropIfExists('gift_recipients');
    }
};
