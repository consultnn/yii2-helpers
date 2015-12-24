<?php

namespace consultnn\helpers;

class Schedule
{
    const DIRECTION_FROM = 1;
    const DIRECTION_TO = 1;

    /**
     * @return array
     */
    public static function aroundWork()
    {
        $dayOfWeek = date('D');

        $match = [
            "_schedule.{$dayOfWeek}" => [
                '$elemMatch' => [
                    'from.hours' => 0,
                    'to.hours' => 24
                ]
            ]
        ];

        return $match;
    }

    public static function workFromTo($value, $direction = self::DIRECTION_FROM)
    {
        $dayOfWeek = date('D');

        if ($direction === self::DIRECTION_TO) {
            $match = [
                "_schedule.{$dayOfWeek}" => [
                    '$elemMatch' => [
                        'from.hours' => [
                            '$lte' => (int) $value
                        ]
                    ],
                ]
            ];
        }

        $match = [
            "_schedule.{$dayOfWeek}" => [
                '$elemMatch' => [
                    'from.hours' => [
                        '$gte' => (int) $value
                    ]
                ]
            ]
        ];

        return $match;
    }
}