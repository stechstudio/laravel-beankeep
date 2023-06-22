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
        Schema::create('journal_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\STS\Beankeep\Laravel\Models\Account::class);
            $table->foreignIdFor(\STS\Beankeep\Laravel\Models\Transaction::class);

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
        Schema::dropIfExists('journal_line_items');
    }
};
