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
        Schema::create('qarors', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('created_date')->nullable();
            $table->integer('number')->nullable();
            $table->string('file')->nullable();
            $table->string('pdf_path')->nullable();
            $table->unsignedBigInteger('published_id')->nullable()->unique();
            $table->longText('text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qarors');
    }
};
