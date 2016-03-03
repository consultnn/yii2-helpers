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
        $range = Filter::dateRange(['min' => '12.01.2016', 'max' => '12.01.2016']);
        $this->assertEquals($range['$gte'], '1452546000'); // 12.01.2016 00:00:00
        $this->assertEquals($range['$lte'], '1452632399'); // 12.01.2016 23:59:59

        $range = Filter::dateRange(['min' => '09.01.2016', 'max' => '12.01.2016']);
        $this->assertEquals($range['$gte'], '1452286800'); // 09.01.2016 00:00:00
        $this->assertEquals($range['$lte'], '1452632399'); // 12.01.2016 23:59:59
    }
}
