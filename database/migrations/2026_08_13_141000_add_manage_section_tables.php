<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sliders')) {
            Schema::create('sliders', function (Blueprint $table) {
                $table->id();
                $table->string('image');
                $table->string('title');
                $table->string('sub_title')->nullable();
                $table->string('btn_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('hero_banners')) {
            Schema::create('hero_banners', function (Blueprint $table) {
                $table->id();
                $table->string('banner_one')->nullable();
                $table->string('title_one')->nullable();
                $table->string('btn_url_one')->nullable();
                $table->string('banner_two')->nullable();
                $table->string('title_two')->nullable();
                $table->string('btn_url_two')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('offer_sliders')) {
            Schema::create('offer_sliders', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('our_features')) {
            Schema::create('our_features', function (Blueprint $table) {
                $table->id();
                $table->string('icon');
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('social_links')) {
            Schema::create('social_links', function (Blueprint $table) {
                $table->id();
                $table->string('icon');
                $table->string('url');
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('our_features');
        Schema::dropIfExists('offer_sliders');
        Schema::dropIfExists('hero_banners');
        Schema::dropIfExists('sliders');
    }
};
