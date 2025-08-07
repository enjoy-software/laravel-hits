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
        Schema::create('hits', function (Blueprint $table) {
            $table->comment('Table for tracking page views and visits');
            $table->id();
            $table->morphs('hittable');
            $table->ipAddress('ip');
            $table->text('user_agent');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->noActionOnDelete()
                ->noActionOnUpdate();
            $table->string('referer', 500)->nullable()->comment('HTTP referer URL');
            $table->string('method', 10)->default('GET')->comment('HTTP request method');
            $table->text('url')->nullable()->comment('Full request URL');
            $table->timestamps();

            $table->index(['ip', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hits');
    }
};
