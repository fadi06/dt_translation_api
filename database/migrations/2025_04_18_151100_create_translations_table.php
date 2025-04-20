<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->index();
            $table->unsignedBigInteger('locale_id')->index();
            $table->text('content');
            $table->unique(['key', 'locale_id'], 'unique_translation_key_locale');
            $table->timestamps();
        });

        // Add composite index with key length on 'content'
        // Apply prefix index only if not using SQLite
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('CREATE INDEX idx_locale_key_content ON translations (locale_id, `key`, `content`(255))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
