<?php
/**
 * Created by gerk on 14.11.17 09:00
 */

namespace PeekAndPoke\Component\Emitter\Stubs;

use PeekAndPoke\Component\Emitter\Event;
use PeekAndPoke\Component\Emitter\Listener;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestListener implements Listener
{
    private $invokedCount = 0;

    /**
     * @param Event $event
     */
    public function __invoke($event)
    {
        $this->invokedCount++;
    }

    /**
     * @return int
     */
    public function getInvokedCount()
    {
        return $this->invokedCount;
    }
}
