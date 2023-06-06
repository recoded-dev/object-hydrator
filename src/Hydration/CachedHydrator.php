<?php

namespace Recoded\ObjectHydrator\Hydration;

use Recoded\ObjectHydrator\Contracts\Hydrator;
use RuntimeException;

class CachedHydrator implements Hydrator
{
    /**
     * @var array<class-string, \Recoded\ObjectHydrator\Hydration\Plan<object>>
     */
    protected array $cache;

    /**
     * Create a new CachedHydrator instance.
     *
     * @param string $path
     * @return void
     */
    public function __construct(
        public readonly string $path,
    ) {
    }

    /**
     * Hydrate an object from raw data.
     *
     * @template T of object
     * @param array<array-key, mixed>|object $data
     * @param class-string<T> $type
     * @return T
     */
    public function hydrate(array|object $data, string $type): object
    {
        $cache = $this->getCache();

        if (!array_key_exists($type, $cache)) {
            throw new RuntimeException('Cache missed for type: ' . $type);
        }

        /** @var \Recoded\ObjectHydrator\Hydration\Plan<T> $plan */ // phpcs:ignore
        $plan = $cache[$type];

        return PlanExecutor::execute($type, $plan, $data, $this);
    }

    /**
     * Read and unserialize the content of the given path.
     *
     * @return array<class-string, \Recoded\ObjectHydrator\Hydration\Plan<object>>
     */
    protected function getCache(): array
    {
        if (isset($this->cache)) {
            return $this->cache;
        }

        $resource = fopen($this->path, 'r');

        if ($resource === false) {
            throw new RuntimeException('Unable to open file.');
        }

        $header = fread($resource, 50);

        if ($header === false) {
            throw new RuntimeException('Unable to read file.');
        }

        $pattern = '/^<\?php\n\n\/\/\sDUMP_VERSION:\s(\d+)\n\nreturn/';

        if (preg_match($pattern, $header, $matches) !== 1) {
            throw new RuntimeException('Invalid file header.');
        }

        if (intval($matches[1]) !== Plan::VERSION) {
            throw new RuntimeException('Invalid cached plan version.');
        }

        return $this->cache = require $this->path;
    }
}
