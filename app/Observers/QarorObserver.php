<?php

namespace App\Observers;

use App\Models\Qaror;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QarorObserver
{
    /**
     * Handle the Qaror "created" event.
     */
    public function created(Qaror $qaror): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Qaror "updated" event.
     */
    public function updated(Qaror $qaror): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Qaror "deleted" event.
     */
    public function deleted(Qaror $qaror): void
    {
        $this->clearCache();
    }

    /**
     * Clear all qaror-related caches
     */
    protected function clearCache(): void
    {
        // Clear home page cache (first 10 pages)
        $perPage = config('qaror.items_per_page', 25);
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("home_qarorlar_page_{$page}_per_{$perPage}");
        }

        // Clear years cache
        Cache::forget('qarorlar_years');

        // Clear search cache (by pattern)
        if (config('cache.default') === 'database') {
            DB::table(config('cache.stores.database.table', 'cache'))
                ->where('key', 'like', '%ajax_search_%')
                ->delete();
        }
    }
}
