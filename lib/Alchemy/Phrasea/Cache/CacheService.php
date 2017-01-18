<?php

namespace Alchemy\Phrasea\Cache;

use Alchemy\Phrasea\BaseApplication;

class CacheService
{
    /**
     * @var BaseApplication
     */
    private $app;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param BaseApplication $application
     * @param Cache $cache
     */
    public function __construct(BaseApplication $application, Cache $cache)
    {
        $this->app = $application;
        $this->cache = $cache;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param \base $base
     * @param string|null $option
     * @return mixed
     */
    public function getDataFromCache(\base $base, $option = null)
    {
        if ($base->get_base_type() == \base::DATA_BOX) {
            \cache_databox::refresh($this->app, $base->getId());
        }

        $data = $this->cache->get($base->get_cache_key($option));

        if (is_object($data) && method_exists($data, 'hydrate')) {
            $data->hydrate($this->app);
        }

        return $data;
    }

    /**
     * @param \base $base
     * @param mixed $value
     * @param string|null $option
     * @param int $duration
     * @return bool
     */
    public function writeDataToCache(\base $base, $value, $option = null, $duration = 0)
    {
        return $this->cache->save($base->get_cache_key($option), $value, $duration);
    }

    /**
     * @param \base $base
     * @param string|null $option
     * @return Cache|bool
     */
    public function deleteDataFromCache(\base $base, $option = null)
    {
        /** @var \appbox $appbox */
        $appbox = $base->get_base_type() == \base::APPLICATION_BOX ? $base : $base->get_appbox();

        if ($option === \appbox::CACHE_LIST_BASES) {
            $keys = [$base->get_cache_key(\appbox::CACHE_LIST_BASES)];

            \phrasea::reset_sbasDatas($appbox);
            \phrasea::reset_baseDatas($appbox);
            \phrasea::clear_sbas_params($this->app);

            return $this->cache->deleteMulti($keys);
        }

        if (is_array($option)) {
            foreach ($option as $key => $value) {
                $option[$key] = $base->get_cache_key($value);
            }

            return $this->cache->deleteMulti($option);
        }

        return $this->cache->delete($base->get_cache_key($option));
    }
}
