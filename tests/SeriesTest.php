<?php

namespace CascadeEnergy\Tests\Diagnostics;

use CascadeEnergy\Diagnostics\Series;

class SeriesTest extends \PHPUnit_Framework_TestCase
{
    /** @var Series */
    private $series;

    public function setUp()
    {
        $this->series = new Series();
    }

    public function testSeveralDiagnosticsShouldBeAbleToBeCombined()
    {
        $foo = $this->getMock('CascadeEnergy\Diagnostics\DiagnosticInterface');
        $bar = $this->getMock('CascadeEnergy\Diagnostics\DiagnosticInterface');
        $baz = $this->getMock('CascadeEnergy\Diagnostics\DiagnosticInterface');

        /** @noinspection PhpParamsInspection */
        $this->series->addDiagnostic($foo);

        /** @noinspection PhpParamsInspection */
        $this->series->addDiagnostic($bar);

        /** @noinspection PhpParamsInspection */
        $this->series->addDiagnostic($baz);

        $this->assertAttributeEquals([$foo, $bar, $baz], 'diagnosticList', $this->series);
    }

    public function testItPassesIfAllDiagnosticsInTheSeriesPass()
    {
        $foo = $this->getMock('CascadeEnergy\Diagnostics\DiagnosticInterface');
        $foo->expects($this->once())->method('isOk')->willReturn(true);

        $bar = $this->getMock('CascadeEnergy\Diagnostics\DiagnosticInterface');
        $bar->expects($this->once())->method('isOk')->willReturn(true);

        /** @noinspection PhpParamsInspection */
        $this->series->addDiagnostic($foo);

        /** @noinspection PhpParamsInspection */
        $this->series->addDiagnostic($bar);

        $this->assertTrue($this->series->isOk());
    }

    public function testItFailsIfAnyDiagnosticInTheSeriesFails()
    {
        $foo = $this->getMock('CascadeEnergy\Diagnostics\DiagnosticInterface');
        $foo->expects($this->once())->method('isOk')->willReturn(true);

        $bar = $this->getMock('CascadeEnergy\Diagnostics\DiagnosticInterface');
        $bar->expects($this->once())->method('isOk')->willReturn(false);

        /** @noinspection PhpParamsInspection */
        $this->series->addDiagnostic($foo);

        /** @noinspection PhpParamsInspection */
        $this->series->addDiagnostic($bar);

        $this->assertFalse($this->series->isOk());
    }
}
