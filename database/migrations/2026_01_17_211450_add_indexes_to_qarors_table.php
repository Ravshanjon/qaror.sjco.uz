<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qarors', function (Blueprint $table) {
            // Add indexes for frequently queried columns
            $table->index('title', 'qarors_title_index'); // For search queries
            $table->index('number', 'qarors_number_index'); // For sorting/filtering
            $table->index('created_date', 'qarors_created_date_index'); // For year filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qarors', function (Blueprint $table) {
            $table->dropIndex('qarors_title_index');
            $table->dropIndex('qarors_number_index');
            $table->dropIndex('qarors_created_date_index');
        });
    }
};
