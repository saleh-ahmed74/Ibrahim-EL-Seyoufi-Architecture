<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('category_id')
                ->constrained('projects')
                ->nullOnDelete();

            $table->index(['parent_id', 'deleted_at', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropIndex(['parent_id', 'deleted_at', 'sort_order']);
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
