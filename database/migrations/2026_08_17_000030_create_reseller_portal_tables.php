<?php

declare(strict_types=1);

use App\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('reseller_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
            $table->index(['reseller_id', 'type']);
        });

        Schema::create('reseller_brandings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reseller_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('brand_name');
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('primary_color', 32)->default('#2563eb');
            $table->string('secondary_color', 32)->default('#0f172a');
            $table->string('support_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->string('custom_domain')->nullable()->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_brandings');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['reseller_id']);
            $table->dropIndex(['reseller_id', 'type']);
            $table->dropColumn('reseller_id');
        });
    }
};
