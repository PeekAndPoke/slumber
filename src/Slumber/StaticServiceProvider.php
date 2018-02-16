<?php
/**
 * Created by gerk on 16.02.18 16:45
 */

namespace PeekAndPoke\Component\Slumber;

use Psr\Container\ContainerInterface;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class StaticServiceProvider implements ContainerInterface
{
    /**
     * @var array
     */
    private $instances = [];

    public function set($id, $instance): StaticServiceProvider
    {
        $this->instances[$id] = $instance;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->instances[$id] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return isset($this->instances[$id]) && $this->instances[$id] !== null;
    }
}
