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
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'name')) {
                $table->string('name')->after('id');
            }

            if (! Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }

            if (! Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete()->after('slug');
            }

            if (! Schema::hasColumn('categories', 'position')) {
                $table->unsignedInteger('position')->default(0)->after('parent_id');
            }

            if (! Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('position');
            }

            if (! Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable()->after('image');
            }

            if (! Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('icon');
            }

            if (! Schema::hasColumn('categories', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_active');
            }
        });

        if (! Schema::hasTable('category_product')) {
            Schema::create('category_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['category_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('category_product')) {
            Schema::drop('category_product');
        }

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            $columns = [
                'is_featured',
                'is_active',
                'icon',
                'image',
                'position',
                'slug',
                'name',
            ];

            $existingColumns = array_filter($columns, fn (string $column) => Schema::hasColumn('categories', $column));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
