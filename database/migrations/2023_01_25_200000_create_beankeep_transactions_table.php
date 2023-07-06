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
        Schema::create('beankeep_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\STS\Beankeep\Models\SourceDocument::class);
            $table->nullableMorphs('keepable');

            $table->date('date');

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
        Schema::dropIfExists('beankeep_transactions');
    }
};
