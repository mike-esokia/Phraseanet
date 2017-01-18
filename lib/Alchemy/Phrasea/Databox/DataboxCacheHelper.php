<?php

namespace Alchemy\Phrasea\Databox;

use Alchemy\Phrasea\Cache\CacheService;

class DataboxCacheHelper
{
    const CACHE_META_STRUCT = 'meta_struct';
    const CACHE_THESAURUS = 'thesaurus';
    const CACHE_COLLECTIONS = 'collections';
    const CACHE_STRUCTURE = 'structure';
    const CACHE_CGUS = 'cgus';

    /**
     * @var CacheService
     */
    private $cacheService;

    /**
     * @param CacheService $cacheService
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }


}
