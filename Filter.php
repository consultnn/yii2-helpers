<?php

namespace consultnn\helpers;

/**
 * Class Filter
 * @package common\helpers
 */
class Filter
{
    /**
     * @param $value
     * @return int|null
     */
    public static function int($value)
    {
        return $value === '' || $value === null ? null : (int) $value;
    }

    /**
     * @param $value
     * @return bool|null
     */
    public static function boolean($value)
    {
        return $value === '' || $value === null ? null : (bool) $value;
    }

    /**
     * @param $value
     * @return \MongoRegex|null
     */
    public static function regex($value)
    {
        return !empty($value) ? new \MongoRegex("/{$value}/i") : null;
    }

    /**
     * @param $date
     * @return array
     */
    public static function dateRange($date)
    {
        $result = [];
        if (!empty($date['min'])) {
            if (!is_numeric($date['max'])) {
                $date['min'] = strtotime($date['min']);
            }

            $result['$gte'] = $date['min'];
        }

        if (!empty($date['max'])) {
            if (!is_numeric($date['max'])) {
                $date['max'] = strtotime($date['max']);
            }

            $result['$lte'] = $date['max'];
        }

        return empty($result) ? null : $result;
    }

    /**
     * @param $date
     * @return array|null
     */
    public static function date($date)
    {
        if (empty($date)) {
            return null;
        }

        $timestamp = strtotime($date);
        return ['$gte' => $timestamp, '$lte' => $timestamp + 60*60*24 ];
    }

    /**
     * @param $id
     * @return \MongoId|null
     */
    public static function mongoId($id)
    {
        if (\MongoId::isValid($id)) {
            return new \MongoId($id);
        }
        return null;
    }

    /**
     * @param $ids
     * @return array|null
     */
    public static function mongoIdArray($ids)
    {
        if (empty($ids)) {
            return null;
        }

        $result = [];
        foreach ($ids as $id) {
            if (\MongoId::isValid($id)) {
                $result[] = new \MongoId($id);
            }
        }
        return $result;
    }

    /**
     * @param $string
     * @return string
     */
    public static function underscoreToCamelCase($string)
    {
        $words = explode('_', strtolower($string));

        $return = '';
        foreach ($words as $word) {
            $return .= ucfirst(trim($word));
        }

        return $return;
    }
}
