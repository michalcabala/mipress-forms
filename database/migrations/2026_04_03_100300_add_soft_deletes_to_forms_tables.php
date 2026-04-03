<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('form_submissions', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('form_submission_attachments', function (Blueprint $table): void {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('form_submissions', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('form_submission_attachments', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
