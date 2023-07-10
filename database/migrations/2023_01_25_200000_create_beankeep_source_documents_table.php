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
        Schema::create('beankeep_source_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\STS\Beankeep\Models\Transaction::class);
            $table->nullableMorphs('keepable');

            $table->string('memo');
            $table->string('attachment')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beankeep_source_documents');
    }
};
