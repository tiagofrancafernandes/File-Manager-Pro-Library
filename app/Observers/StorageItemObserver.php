<?php

namespace App\Observers;

use App\Models\StorageItem;

class StorageItemObserver
{
    /**
     * Handle the StorageItem "created" event.
     */
    public function created(StorageItem $storageItem): void
    {
        $storageItem->fillHashedid();
    }

    /**
     * Handle the StorageItem "updated" event.
     */
    public function updated(StorageItem $storageItem): void
    {
        $storageItem->fillHashedid();
    }

    /**
     * Handle the StorageItem "deleted" event.
     */
    public function deleted(StorageItem $storageItem): void
    {
        // TODO: unlink file
    }

    /**
     * Handle the StorageItem "restored" event.
     */
    public function restored(StorageItem $storageItem): void
    {
        $storageItem->fillHashedid();
    }

    /**
     * Handle the StorageItem "force deleted" event.
     */
    public function forceDeleted(StorageItem $storageItem): void
    {
        //
    }
}
