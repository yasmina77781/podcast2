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
       Schema::create('episodes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('podcast_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->text('description');
    $table->string('audio_url');
    $table->integer('duration')->nullable();
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
