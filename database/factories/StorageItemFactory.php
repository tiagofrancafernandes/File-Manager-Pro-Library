<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\StorageItemTypeEnum;
use App\Enums\StorageItemVisibilityEnum;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StorageItem>
 */
class StorageItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hashedid' => null,
            'type_enum' => StorageItemTypeEnum::FILE,
            'user_id' => User::inRandomOrder()->first()?->id ?: User::factory()?->create()?->id,
            'name' => fake()->word() . '.pdf',
            'favorite' => fake()->boolean(30),
            'visibility_enum' => StorageItemVisibilityEnum::PRIVATE ,
            'parent_folder' => null,
            'mime_type' => 'application/pdf',
            'size' => 14567,
            'disk' => 'public',
            'path' => fn (array $attr) => static::storeFakeFile(attr: $attr)['path'] ?? null,
            'extra_data' => [],
            'password' => null,
        ];
    }

    public static function storeFakeFile(?string $fileName = null, array $attr = []): array
    {
        $disk = $attr['disk'] ?? 'public';

        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter $storage
         */
        $storage = Storage::disk($disk);
        $fileName ??= md5(uniqid()) . '.pdf';
        $dirToSave = 'uploads';
        $path = implode('/', [$dirToSave, $fileName]);
        $storage->put($path, file_get_contents(base_path('database/seeders/data/Laravel.pdf')));

        return [
            'path' => $path,
            'fileName' => $fileName,
            'storage' => $storage,
            'disk' => $disk,
            'extra_data' => [
                'attr' => $attr,
            ],
        ];
    }

    public function asDir(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_enum' => StorageItemTypeEnum::DIR?->value,
            'disk' => null,
            'parent_folder' => null,
            'mime_type' => null,
            'size' => null,
            'path' => null,
            'password' => null,
        ]);
    }
}
