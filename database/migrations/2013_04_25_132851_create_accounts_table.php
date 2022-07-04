<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // necessary for SQLlite
        Schema::enableForeignKeyConstraints();

        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('shortcode');
            $table->uuid('uuid');
            $table->boolean('is_temporary')->default(false);
            $table->timestamps();
            $table->index('shortcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
