<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StorageItemVisibilityEnum;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_items', function (Blueprint $table) {
            $table->id()->index();
            $table->string('hashedid')->index()->nullable();
            $table->uuid()->unique()->index();
            $table->integer('type_enum')->index(); // app/Enums/StorageItemTypeEnum.php
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name')->index();
            $table->boolean('favorite')->index()->default(false);
            $table->integer('visibility_enum')->index()->default(StorageItemVisibilityEnum::PRIVATE?->value); // app/Enums/StorageItemVisibilityEnum.php
            $table->unsignedBigInteger('parent_folder')->index()->nullable();
            $table->string('mime_type')->index()->nullable();
            $table->string('size')->index()->nullable();

            $table->string('disk')->index()->nullable()->default('public');
            $table->string('path')->nullable();
            $table->string('password')->nullable();
            $table->json('extra_data')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // $table->unique([
            //     'user_id',
            //     'name',
            //     'parent_folder',
            // ]);

            $table->foreign('user_id')->references('id')
                ->on('users')->onDelete('cascade'); // cascade|set null

            $table->foreign('parent_folder')->references('id')
                ->on('storage_items')->onDelete('cascade'); // cascade|set null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_items');
    }
};
