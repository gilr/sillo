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
        Schema::create('posts', function(Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('body');
            $table->text('excerpt');
            $table->boolean('active')->default(false);
            $table->string('image')->nullable();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('category_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('serie_id')->nullable()->default(null);
            $table->foreign('serie_id')
                    ->references('id')
                    ->on('series')
                    ->nullable()
                    ->default(null)
                    ->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')->nullable()->default(null);
            $table->foreign('parent_id')
                    ->references('id')
                    ->on('posts')
                    ->nullable()
                    ->default(null);                   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
