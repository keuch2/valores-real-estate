<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('password');
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('previous_slugs')->nullable();
            $table->string('name');
            $table->string('full_name');
            $table->string('type_label');
            $table->string('location');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('kind', 10)->default('none'); // units | lots | none
            $table->string('hero_image')->nullable();
            $table->boolean('hero_is_render')->default(false);
            $table->string('tagline')->nullable();
            $table->string('short', 500)->nullable();
            $table->text('body')->nullable();
            $table->string('delivery')->nullable();
            $table->string('instagram')->nullable();
            $table->string('brochure_path')->nullable();
            $table->string('masterplan_image')->nullable();
            $table->string('video_path')->nullable();
            $table->string('video_poster')->nullable();
            $table->json('facts')->nullable();
            $table->string('amenities_title')->nullable();
            $table->json('amenities')->nullable();
            $table->string('sports_title')->nullable();
            $table->text('sports_text')->nullable();
            $table->json('sports_phones')->nullable();
            $table->string('compare_type')->nullable();
            $table->string('compare_surface')->nullable();
            $table->string('compare_amenities')->nullable();
            $table->string('compare_environment')->nullable();
            $table->decimal('map_x', 5, 2)->nullable();
            $table->decimal('map_y', 5, 2)->nullable();
            $table->string('map_label_side', 10)->default('top');
            $table->string('whatsapp_number')->nullable();
            $table->string('visit_message', 500)->nullable();
            $table->string('info_message', 500)->nullable();
            $table->string('availability_note', 500)->nullable();
            $table->string('news_tag')->nullable();
            $table->string('import_format', 20)->default('generic');
            $table->string('import_sheet')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('alt')->nullable();
            $table->boolean('is_render')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('project_typologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('specs')->nullable();
            $table->json('images')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Lotes y unidades. Sin columnas de precio: la planilla del cliente trae precios y no se publican.
        Schema::create('project_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('block', 20)->nullable();
            $table->string('number', 20);
            $table->unsignedSmallInteger('floor')->nullable();
            $table->string('type')->nullable();
            $table->decimal('area_m2', 10, 2)->nullable();
            $table->string('orientation')->nullable();
            $table->string('status', 20)->default('disponible');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['project_id', 'block', 'number']);
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 10)->default('external'); // external | article
            $table->string('title', 300);
            $table->string('slug')->nullable()->unique();
            $table->string('external_url', 500)->nullable();
            $table->string('source')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tag')->nullable();
            $table->string('image')->nullable();
            $table->boolean('image_is_render')->default(false);
            $table->string('excerpt', 500)->nullable();
            $table->longText('body')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('meta_description', 300)->nullable();
            $table->timestamps();
        });

        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('advisors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->string('role_label')->default('Asesora comercial');
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key', 40)->unique();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->json('content')->nullable();
            $table->timestamps();
        });

        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // contact | careers | investor | project
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('country')->nullable();
            $table->string('area')->nullable();
            $table->string('modality')->nullable();
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('cv_original_name')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->decimal('recaptcha_score', 3, 2)->nullable();
            $table->boolean('is_spam_suspect')->default(false);
            $table->timestamp('mail_sent_at')->nullable();
            $table->text('mail_error')->nullable();
            $table->string('whatsapp_url', 2000)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('advisors');
        Schema::dropIfExists('board_members');
        Schema::dropIfExists('news');
        Schema::dropIfExists('project_units');
        Schema::dropIfExists('project_typologies');
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('projects');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_active'));
    }
};
