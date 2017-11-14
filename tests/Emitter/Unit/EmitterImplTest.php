<?php
/**
 * Created by gerk on 14.11.17 08:07
 */

namespace PeekAndPoke\Component\Emitter\Unit;

use PeekAndPoke\Component\Emitter\EmitterImpl;
use PeekAndPoke\Component\Emitter\Event;
use PeekAndPoke\Component\Emitter\EventX;
use PeekAndPoke\Component\Emitter\Stubs\UnitTestListener;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EmitterImplTest extends TestCase
{
    public function testDisabledMustNotEmit()
    {
        $subject = new EmitterImpl();
        $subject->enable(false);

        $eventName = 'evt';
        $value     = new \ArrayObject();

        $subject->bind($eventName, function (Event $evt) use (&$value) {
            $value['something'] = $evt->getPayload();
        });

        $subject->emit(new EventX($eventName));

        $this->assertCount(
            0,
            $value,
            'Event must not be emitted wheh emitter is disabled'
        );
    }

    public function testEnabledMustEmit()
    {
        $subject = new EmitterImpl();

        $eventName = 'evt';
        $value     = new \ArrayObject();

        $subject->bind($eventName, function (Event $evt) use (&$value) {
            $value['something'] = $evt->getPayload();
        });

        $subject->emit(new EventX($eventName));

        $this->assertCount(
            1,
            $value,
            'Event must not be emitted wheh emitter is disabled'
        );
    }

    public function testCorrectListenersMustBeInvoked()
    {
        $subject = new EmitterImpl();

        $evtOne     = 'evtOne';
        $evtTwo     = 'evtTwo';
        $counterOne = 0;
        $counterTwo = 0;

        // bind the first event twice
        $subject->bind($evtOne, function (EventX $evt) use (&$counterOne) {
            $counterOne += $evt->getPayload();
        });
        $subject->bind($evtOne, function (EventX $evt) use (&$counterOne) {
            $counterOne += $evt->getPayload() * 2;
        });

        // bind the second event once
        $subject->bind($evtTwo, function (EventX $evt) use (&$counterTwo) {
            $counterTwo += $evt->getPayload() * 5;
        });

        // emit the first event twice
        $subject->emit(new EventX($evtOne, 10));
        $subject->emit(new EventX($evtOne, 10));

        // emit the second event twice
        $subject->emit(new EventX($evtTwo, 10));
        $subject->emit(new EventX($evtTwo, 10));

        // emit an event that nobody has subscribed to
        $subject->emit(new EventX('UNKNOWN', 20));

        // counterOne must now be 60 = 0 + (10 + 10*2) + (10 + 10*2)
        $this->assertSame(
            60,
            $counterOne,
            'The correct listeners must be called'
        );

        // counterTwo must now be 100 = 0 + 10*5 + 10*5
        $this->assertSame(
            100,
            $counterTwo,
            'The correct listeners must be called'
        );
    }

    public function testListenerClassIsInvoked()
    {
        $subject  = new EmitterImpl();
        $listener = new UnitTestListener();

        $subject->bind('evt', $listener);

        $subject->emit(new EventX('evt'));
        $subject->emit(new EventX('evt'));

        $this->assertSame(
            2,
            $listener->getInvokedCount(),
            'Listener class must be invoked correct number of times'
        );
    }
}
