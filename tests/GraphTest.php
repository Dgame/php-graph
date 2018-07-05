<?php

namespace Dgame\Graph\Test;

use Dgame\Graph\Context;
use Dgame\Graph\Graph;
use PHPUnit\Framework\TestCase;

/**
 * Class GraphTest
 */
final class GraphTest extends TestCase
{
    /**
     *
     */
    public function testTraverse()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
            print 'A' . PHP_EOL;
        });
        $graph->insert('B', function () {
            print 'B' . PHP_EOL;
        });
        $graph->insert('C', function () {
            print 'C' . PHP_EOL;
        });

        $graph->setTransitions(['A' => 'C']);
        $graph->setTransitions(['C' => 'B']);

        $context = new Context();
        ob_start();
        $graph->launch('A', $context);
        $content = ob_get_clean();

        $this->assertEquals(['A', 'C', 'B'], explode(PHP_EOL, trim($content)));
    }

    /**
     * @throws \Exception
     */
    public function testIsCyclic()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
        });
        $graph->insert('B', function () {
        });
        $graph->insert('C', function () {
        });

        $graph->setTransitions(['A' => 'C', 'C' => 'B']);

        $this->assertFalse($graph->isCyclic('A'));
        $this->assertFalse($graph->isCyclic('B'));
        $this->assertFalse($graph->isCyclic('C'));

        $graph->setTransitions(['B' => 'A']);

        $this->assertTrue($graph->isCyclic('A'));
        $this->assertTrue($graph->isCyclic('B'));
        $this->assertTrue($graph->isCyclic('C'));
    }

    /**
     * @throws \Exception
     */
    public function testCanReach()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
        });
        $graph->insert('B', function () {
        });
        $graph->insert('C', function () {
        });

        $graph->setTransitions(['A' => 'C']);

        $this->assertFalse($graph->canReach(['A' => 'B']));
        $this->assertTrue($graph->canReach(['A' => 'C']));

        $graph->setTransitions(['C' => 'B']);

        $this->assertTrue($graph->canReach(['A' => 'B']));
    }

    /**
     * @throws \Exception
     */
    public function testGetTargets()
    {
        $graph = new Graph();
        $graph->insert('A', function () {
        });
        $graph->insert('B', function () {
        });
        $graph->insert('C', function () {
        });

        $graph->setTransitions(['A' => 'C']);
        $graph->setTransitions(['C' => 'B']);

        $this->assertEquals(['B'], $graph->getTargets('A'));
        $this->assertEmpty($graph->getTargets('B'));
        $this->assertEquals(['B'], $graph->getTargets('C'));

        $graph->setTransitions(['B' => 'A']);

        $this->assertEmpty($graph->getTargets('A'));
        $this->assertEmpty($graph->getTargets('B'));
        $this->assertEmpty($graph->getTargets('C'));
    }

    public function testTransitions()
    {
        function println(...$what)
        {
            foreach ($what as $value) {
                print $value;
            }

            print PHP_EOL;
        }

        $graph = new Graph();
        $graph->insert('A', function () {
            println('Lade Website');
        });
        $graph->insert('B', function () {
            println('Ist gecached?');
        });
        $graph->insert('C', function () {
            println('Ist Cache veraltet?');
        });
        $graph->insert('D_1', function () {
            println('Ist Server erreichbar?');
        });
        $graph->insert('D_2', function () {
            println('Seite neu laden');
        });
        $graph->insert('F_1', function () {
            println('Startseite gefunden?');
        });
        $graph->insert('F_2', function () {
            println('Generiere Startseite');
        });
        $graph->insert('F_3', function () {
            println('Cache Startseite');
        });
        $graph->insert('G_1', function () {
            println('Nebenseite gefunden?');
        });
        $graph->insert('G_2', function () {
            println('Generiere Nebenseite');
        });
        $graph->insert('G_3', function () {
            println('Cache Nebenseite');
        });
        $graph->insert('H', function () {
            println('Alten Cache verwenden');
        });
        $graph->insert('I', function () {
            println('Lade Seite via Cache');
        });
        $graph->insert('J', function () {
            println('Input Felder ausfüllen');
        });
        $graph->insert('K', function () {
            println('Request absenden');
        });
        $graph->insert('D_B_0', function () {
            println('Existiert ein Cache?');
        });
        $graph->insert('D_B_1', function () {
            println('Es existiert kein Cache und der Server ist nicht erreichbar!');
        });

        /*
        $graph->setTransitions([
            'A'   => 'B',
            'B'   => ['C' => true, 'D_1' => false],
            'C'   => ['D_1' => true, 'I' => false],
            'D_1' => ['D_2' => true, 'D_B_0' => false],
            'D_2' => ['F_1', 'G_1', 'I'],
            'F_1' => ['F_2' => true],
            'F_2' => ['F_3' => true],
            'G_1' => ['G_2' => true],
            'G_2' => ['G_3' => true],
            'D_B_0' => ['H' => true, 'D_B_1' => false],
            'H'   => 'I',
            'I'   => 'J',
            'J'   => 'K'
        ]);
        */

        $graph->setTransitions([
                                   'A'     => 'B',
                                   'B'     => ['C' => true, 'D_1' => false],
                                   'C'     => ['D_1' => true, 'I' => false],
                                   'D_1'   => ['D_2' => true, 'D_B_0' => false],
                                   'D_2'   => ['F_1', 'G_1', 'I'],
                                   'F_1'   => ['F_2' => true, 'F_3' => 'F_2'],
                                   'G_1'   => ['G_2' => true, 'G_3' => 'G_2'],
                                   'D_B_0' => ['H' => true, 'D_B_1' => false],
                                   'H'     => 'I',
                                   'I'     => 'J',
                                   'J'     => 'K'
                               ]);
        ob_start();
        $graph->launch('A');
        $content = ob_get_clean();
        $lines   = explode(PHP_EOL, $content);

        $this->assertEquals(
            [
                'Lade Website',
                'Ist gecached?',
                'Ist Cache veraltet?',
                'Ist Server erreichbar?',
                'Seite neu laden',
                'Startseite gefunden?',
                'Generiere Startseite',
                'Cache Startseite',
                'Nebenseite gefunden?',
                'Generiere Nebenseite',
                'Cache Nebenseite',
                'Lade Seite via Cache',
                'Input Felder ausfüllen',
                'Request absenden'
            ],
            array_filter($lines)
        );
    }
}
