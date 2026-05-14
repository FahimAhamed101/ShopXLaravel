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
        if (! Schema::hasTable('stores')) {
            Schema::create('stores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('seller_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->string('logo')->nullable();
                $table->string('banner')->nullable();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->string('short_description')->nullable();
                $table->longText('long_description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $this->createStoresForExistingVendors();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }

    private function createStoresForExistingVendors(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'user_type')) {
            return;
        }

        DB::table('users')
            ->where('user_type', 'vendor')
            ->orderBy('id')
            ->get(['id', 'name', 'email', 'created_at'])
            ->each(function (object $vendor): void {
                DB::table('stores')->updateOrInsert(
                    ['seller_id' => $vendor->id],
                    [
                        'name' => $vendor->name ?: 'Vendor Store',
                        'email' => $vendor->email,
                        'short_description' => ($vendor->name ?: 'Vendor').' store profile',
                        'created_at' => $vendor->created_at ?? now(),
                        'updated_at' => now(),
                    ]
                );
            });
    }
};
