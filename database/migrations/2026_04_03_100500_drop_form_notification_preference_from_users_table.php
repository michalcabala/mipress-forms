<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'form_notification_preference')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('form_notification_preference');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'form_notification_preference')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->string('form_notification_preference')->default('both')->after('remember_token');
        });
    }
};
