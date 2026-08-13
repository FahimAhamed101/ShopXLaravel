<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'provider_session_id')) {
                $table->string('provider_session_id')->nullable()->unique()->after('transaction_id');
            }

            if (! Schema::hasColumn('orders', 'checkout_cart_ids')) {
                $table->json('checkout_cart_ids')->nullable()->after('shipping_info');
            }
        });

        if (Schema::hasTable('settings')) {
            $legacyKey = DB::table('settings')->where('key', 'stripe_client_id')->value('value');

            if ($legacyKey && ! DB::table('settings')->where('key', 'stripe_key')->exists()) {
                DB::table('settings')->insert([
                    'key' => 'stripe_key',
                    'value' => $legacyKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'provider_session_id')) {
                $table->dropUnique(['provider_session_id']);
                $table->dropColumn('provider_session_id');
            }

            if (Schema::hasColumn('orders', 'checkout_cart_ids')) {
                $table->dropColumn('checkout_cart_ids');
            }
        });
    }
};
