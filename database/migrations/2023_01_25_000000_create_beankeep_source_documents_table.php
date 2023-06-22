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
        Schema::create('beankeep_source_documents', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->string('memo');
            $table->string('attachment')->nullable();
            $table->string('filename')->nullable();
            $table->string('mime_type')->nullable();

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
        Schema::dropIfExists('beankeep_source_documents');
    }
};
