<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beankeep_journals', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('keepable');

            // 1 = jan, 2 = feb, ...
            $table->integer('period');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beankeep_journals');
    }
};
