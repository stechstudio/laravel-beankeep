<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beankeep_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\STS\Beankeep\Models\Account::class);
            $table->foreignIdFor(\STS\Beankeep\Models\Transaction::class);
            $table->nullableMorphs('keepable');

            $table->integer('debit');
            $table->integer('credit');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beankeep_line_items');
    }
};
