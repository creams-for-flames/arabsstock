<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        return;
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `video_tags` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `video_subscriptions` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `video_plans` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `video_likes` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `video_collections` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `video_categories` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `videos` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vector_subscriptions` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vector_likes` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vector_collections` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vector_categories` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vectors_tags_contributor` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vectors_tags` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vectors_reporteds` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vectors` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users_reporteds` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `skills_contacts` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `skills` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `reserved` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `replies` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `paypal_payers` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `password_resets` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `languages` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `keywords` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `jobs` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `image_tags_contributor` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `image_tags` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `image_likes` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `image_collections` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `image_categories` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `images_reporteds` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `images` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `followers` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `failed_jobs` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `email_subscribes` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `contacts` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `computer_vision_video_tags` MODIFY COLUMN id INTEGER (10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `computer_vision_vector_tags` MODIFY COLUMN id INTEGER (10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `computer_vision_image_tags` MODIFY COLUMN id INTEGER (10) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `image_comment_likes` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `video_comment_likes` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `vector_comment_likes` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `comments` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `collection_video` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `collection_vector` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `collection_image` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_video_admins` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_videos` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_contributor_vector` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_vectors` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_image_contributor` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_image` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_admins_vectors` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `category_admins` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `attatchments` MODIFY COLUMN id INTEGER (11) UNSIGNED NOT NULL AUTO_INCREMENT FIRST;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
