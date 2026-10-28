<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gap_queries', function (Blueprint $table): void {
            $table->id();
            $table->text('question');
            $table->decimal('top_score', 8, 6)->default(0);
            $table->boolean('answered')->default(false);
            $table->string('suggested_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gap_queries');
    }
};
