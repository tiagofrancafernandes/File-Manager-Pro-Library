<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\StorageItemTypeEnum;
use App\Enums\StorageItemVisibilityEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 *
 *
 * @property int $id
 * @property string|null $hashedid
 * @property string $uuid
 * @property StorageItemTypeEnum $type_enum
 * @property int $user_id
 * @property string $name
 * @property bool $favorite
 * @property StorageItemVisibilityEnum $visibility_enum
 * @property int|null $parent_folder
 * @property string|null $mime_type
 * @property string|null $size
 * @property string|null $disk
 * @property string|null $path
 * @property mixed|null $password
 * @property \Illuminate\Support\Collection|null $extra_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $file_url
 * @property-read User $owner
 * @method static \Database\Factories\StorageItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereExtraData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereFavorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereHashedid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereParentFolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereTypeEnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem whereVisibilityEnum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StorageItem withoutTrashed()
 * @mixin \Eloquent
 */
class StorageItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    protected $fillable = [
        'uuid',
        'hashedid',
        'type_enum',
        'user_id',
        'name',
        'favorite',
        'visibility_enum',
        'parent_folder',
        'mime_type',
        'size',
        'disk',
        'path',
        'extra_data',
        'password',
    ];

    protected $casts = [
        'type_enum' => StorageItemTypeEnum::class,
        'favorite' => 'boolean',
        'visibility_enum' => StorageItemVisibilityEnum::class,
        'extra_data' => AsCollection::class,
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'typeName',
        'updated_at_humans_label',
        // 'fileUrl',
        // 'fileTemporaryUrl',
        // 'virtualPath',
    ];

    public function uniqueIds(): array
    {
        return [
            'uuid',
        ];
    }

    public function getTypeNameAttribute()
    {
        return $this?->type_enum?->name;
    }

    public function getUpdatedAtHumansLabelAttribute()
    {
        return $this?->updated_at?->diffForHumans();
    }

    public function getVirtualPathAttribute()
    {
        $storageItem = $this;

        $parentFolder = $storageItem?->parent_folder
            ? StorageItem::dirsOnly()
                ->where('id', $storageItem?->parent_folder)
                ->first()
            : null;

        if (is_null($parentFolder)) {
            return null;
        }

        return implode(
            '/',
            collect([
                $parentFolder?->virtualPath ?? $parentFolder?->name ?? null,
            ])
                ->filter()
                    ?->map(fn ($item) => trim($item, '/\\'))
                    ?->toArray()
        );
    }

    /**
     * Get the owner that owns the StorageItem
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeDirsOnly(Builder $query)
    {
        return $query->where('type_enum', StorageItemTypeEnum::DIR?->value);
    }

    public function storage(): ?\Illuminate\Filesystem\FilesystemAdapter
    {
        $disk = $this?->disk ?? config('filesystems.default');

        if (!$disk || !array_key_exists($disk, (array) config('filesystems.disks'))) {
            return null;
        }

        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter $storage
         */
        return Storage::disk($disk);
    }

    public static function rootPath(): string
    {
        return '/';
    }

    public function userBasePath(): ?string
    {
        return $this->owner?->basePath();
    }

    public function userRootPath(): ?string
    {
        return $this->owner?->rootPath();
    }

    public function getFileUrlAttribute()
    {
        return $this?->storage()?->url($this?->path);
    }

    public function getFileTemporaryUrlAttribute()
    {
        $this->getTemporaryUrl();
    }

    public function getTemporaryUrl(null|int $ttl = null)
    {
        $storage = $this->storage();

        if (!$storage || !method_exists($storage, 'getTemporaryUrl')) {
            return null;
        }

        $defaultExpiration = 5;// in minutes
        $ttl = $ttl && $ttl > 0 ? $ttl : $defaultExpiration;

        return $this?->storage()?->temporaryUrl($this?->path, now()->addMinutes($ttl));
    }

    public function fillHashedid(): void
    {
        $storageItem = $this;

        if (filled($storageItem?->hashedid ?? null)) {
            return;
        }

        $storageItem->fill([
            'hashedid' => hashids_encode($storageItem?->id),
        ]);

        $storageItem->saveQuietly();
    }

    public static function fillNulledHashedid(): void
    {
        StorageItem::whereNull('hashedid')
            ->each(
                fn (StorageItem $storageItem) => $storageItem->fillHashedid()
            );
    }

    public static function decodedHashedid(
        mixed $hashedid,
        null|User $user = null,
        null|int|StorageItemTypeEnum $type = null,
    ): null|int|string {
        $hashedid = filled($hashedid) && is_string($hashedid) ? $hashedid : null;

        if ($type && (is_integer($type) || is_a($type, StorageItemTypeEnum::class))) {
            $type = is_integer($type) ? StorageItemTypeEnum::tryFrom($type) : $type;
        }

        if (!$hashedid) {
            return null;
        }

        if ($user) {
            $hashedid = StorageItem::query()
                ->where('user_id', $user?->id)
                ->when(
                    is_a($type, StorageItemTypeEnum::class),
                    fn ($query) => $query?->where('type_enum', $type?->value),
                )
                ->where('hashedid', $hashedid)?->exists() ? $hashedid : null;
        }

        if (!$hashedid) {
            return null;
        }

        $decodedHashedid = $hashedid ? (hashids_decode($hashedid)[0] ?? null) : null;

        if (!$decodedHashedid) {
            return null;
        }

        return $decodedHashedid;
    }

    public static function byHashedid(
        mixed $hashedid,
        null|User $user = null,
        null|int|StorageItemTypeEnum $type = null,
    ): ?static {
        if (!$hashedid) {
            return null;
        }

        $decodedHashedid = static::decodedHashedid($hashedid, $user, $type);

        if (!$decodedHashedid) {
            return null;
        }

        return StorageItem::query()
            ->where('id', $decodedHashedid)
            ->where('hashedid', $hashedid)
            ->when($user, fn ($query) => $query->where('user_id', $user?->id))
            ->first() ?: null;
    }

    public static function baseOfUserOrCreate(
        User $user,
    ): static {
        $storageItem = StorageItem::query()
            ->where('user_id', $user?->id)
            ->where('type_enum', StorageItemTypeEnum::DIR?->value)
            ->where('name', $user?->username)
            ->first();

        if ($storageItem) {
            return $storageItem;
        }

        return StorageItem::factory()->asDir()->create([
            'name' => $user?->username,
            'user_id' => $user?->id,
        ]);
    }

    public static function getTargetDirFromRequest(Request $request): static
    {
        /** @var User $user */
        $user = $request?->user();
        $base = str_or_null($request?->input('base')) ?? str_or_null($request?->input('to'));

        $to = StorageItem::byHashedid(
            $base,
            $user,
            StorageItemTypeEnum::DIR,
        );

        return $to ?: StorageItem::baseOfUserOrCreate($user);
    }

    public static function storeFile(
        User $user,
        ?string $sourceFile = null,
        ?string $originalName = null,
        ?UploadedFile $uploadedFile = null,
        ?StorageItem $base = null,
        bool $storeItem = true,
        array $attr = [],
    ): ?array {
        $uploadedFile ??= $attr['uploadedFile'] ?? null;

        /**
         * @var ?UploadedFile $uploadedFile
         */
        $uploadedFile = is_a($uploadedFile, UploadedFile::class) ? $uploadedFile : null;

        $sourceFile = $sourceFile ?: $uploadedFile?->path();
        $originalName = $originalName ?: $uploadedFile?->getClientOriginalName();

        if (!is_file($sourceFile)) {
            return null;
        }

        if ($base?->type_enum === StorageItemTypeEnum::DIR) {
            $base = $base?->user_id === $user?->id || $user?->can('store_on_dir', $base) ? $base : null;
        }

        $password = str_or_null($attr['password'] ?? null);
        $disk = str_or_null($attr['disk'] ?? null) ?: 'public';

        /**
         * @var \Illuminate\Filesystem\FilesystemAdapter $storage
         */
        $storage = Storage::disk($disk);
        $fileName = md5(uniqid()) . '.pdf';
        $dirToSave = 'uploads';
        $path = implode('/', [$dirToSave, $fileName]);
        $storage->put($path, file_get_contents($sourceFile));

        $storageItem = $storeItem ? StorageItem::create([
            'type_enum' => StorageItemTypeEnum::FILE,
            'user_id' => $user?->id,
            'name' => /* date('his') . */ $originalName,
            'parent_folder' => $base?->id,
            'favorite' => boolval($attr['favorite'] ?? null),
            'visibility_enum' => $attr['visibility_enum'] ?? StorageItemVisibilityEnum::PRIVATE ,
            'mime_type' => $attr['mime_type'] ?? $uploadedFile?->getMimeType() ?? null,
            'size' => $attr['size'] ?? $uploadedFile?->getSize() ?? null,
            'disk' => $disk,
            'path' => $attr['path'] ?? $path ?? null,
            'extra_data' => $attr['extra_data'] ?? null,
            'password' => $password ? \Hash::make($password) : null,
        ]) : null;

        return [
            'storageItem' => $storageItem,
            'path' => $path,
            'fileName' => $fileName,
            'storage' => $storage,
            'disk' => $disk,
            'extra_data' => $attr['extra_data'] ?? [],
        ];
    }

    public function scopePdfOnly(Builder $query)
    {
        return $query->where('mime_type', 'application/pdf');
    }
}
