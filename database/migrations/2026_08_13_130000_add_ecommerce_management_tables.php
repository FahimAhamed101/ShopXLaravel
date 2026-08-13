<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('flash_sales')) {
            Schema::create('flash_sales', function (Blueprint $table): void {
                $table->id();
                $table->date('sale_start');
                $table->date('sale_end');
                $table->json('products');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('coupons') && ! Schema::hasColumn('coupons', 'usage_limit_per_customer')) {
            Schema::table('coupons', function (Blueprint $table): void {
                $table->unsignedInteger('usage_limit_per_customer')->default(1)->after('usage_limit_per_coupon');
            });
        }

        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'coupon_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->unsignedBigInteger('coupon_id')->nullable()->index()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'coupon_id')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->dropColumn('coupon_id');
            });
        }

        if (Schema::hasTable('coupons') && Schema::hasColumn('coupons', 'usage_limit_per_customer')) {
            Schema::table('coupons', function (Blueprint $table): void {
                $table->dropColumn('usage_limit_per_customer');
            });
        }

        Schema::dropIfExists('flash_sales');
    }
};
