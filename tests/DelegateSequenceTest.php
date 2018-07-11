<?php

namespace Dgame\Graph\Test;

use Dgame\Graph\Context;
use Dgame\Graph\DelegateSequence;
use PHPUnit\Framework\TestCase;

/**
 * Class DelegateSequenceTest
 * @package Dgame\Graph\Test
 */
final class DelegateSequenceTest extends TestCase
{
    public function testTraverse()
    {
        $sequence = new DelegateSequence();
        $sequence->willExecute('A', function () {
            print $this->getName() . PHP_EOL;
        });
        $sequence->willExecute('D', function () {
            print $this->getName() . PHP_EOL;
        });
        $sequence->willExecuteAfter('A', function () {
            print $this->getName() . PHP_EOL;
        }, 'B');
        $sequence->willExecuteBefore('D', function () {
            print $this->getName() . PHP_EOL;
        }, 'C');

        ob_start();
        $sequence->execute();
        $content = ob_get_clean();

        $this->assertEquals(['A', 'B', 'C', 'D'], explode(PHP_EOL, trim($content)));
    }

    public function testPush()
    {
        $sequence = new DelegateSequence();
        $sequence->push('A', function () {
            print $this->getName() . PHP_EOL;
        });
        $sequence->push('B', function () {
            print $this->getName() . PHP_EOL;
        });
        $sequence->push('C', function () {
            print $this->getName() . PHP_EOL;
        });
        $sequence->push('D', function () {
            print $this->getName() . PHP_EOL;
        });

        ob_start();
        $sequence->execute();
        $content = ob_get_clean();

        $this->assertEquals(['D', 'C', 'B', 'A'], explode(PHP_EOL, trim($content)));

        ob_start();
        $sequence->startWith('B');
        $content = ob_get_clean();

        $this->assertEquals(['B', 'A'], explode(PHP_EOL, trim($content)));
    }

    public function testWhile()
    {
        $sequence = new DelegateSequence();
        $sequence->willExecute('X', function (Context $context) {
            $i = $context->getOrSet('i', 0);
            print $i;
            $context->set('i', $i + 1);
        })->while(function (Context $context): bool {
            return $context->getAsInt('i') < 10;
        });

        $context = new Context();
        ob_start();
        $sequence->execute($context);
        $content = ob_get_clean();

        $this->assertEquals('0123456789', $content);
    }

    public function testUntil()
    {
        $sequence = new DelegateSequence();
        $sequence->willExecute('Y', function (Context $context) {
            $i = $context->getOrSet('i', 0);
            print $i;
            $context->set('i', $i + 1);
        })->until(function (Context $context): bool {
            return $context->getAsInt('i') >= 20;
        });

        $context = new Context();
        $context->set('i', 10);

        ob_start();
        $sequence->execute($context);
        $content = ob_get_clean();

        $this->assertEquals('10111213141516171819', $content);
    }
}
