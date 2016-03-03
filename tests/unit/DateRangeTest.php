<?php

use consultnn\helpers\Filter;

class DateRangeTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        $range = Filter::dateRange(['min' => '12.01.2016 04:53:22', 'max' => '12.01.2016 03:32:41']);
        $this->assertEquals($range['$gte'], '1452546000'); // 12.01.2016 00:00:00
        $this->assertEquals($range['$lt'], '1452632400'); // 12.01.2016 23:59:59

        $range = Filter::dateRange(['min' => '09.01.2016 08:42:26', 'max' => '12.01.2016 06:39:41']);
        $this->assertEquals($range['$gte'], '1452286800'); // 09.01.2016 00:00:00
        $this->assertEquals($range['$lt'], '1452632400'); // 12.01.2016 23:59:59
    }
}
