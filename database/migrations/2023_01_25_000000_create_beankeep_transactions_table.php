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
        Schema::create('beankeep_transactions', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('keepable');

            $table->date('date');
            $table->boolean('posted')->default(false);
            $table->string('memo');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beankeep_transactions');
    }
};
