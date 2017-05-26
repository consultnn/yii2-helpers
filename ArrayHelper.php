<?php

namespace consultnn\helpers;

use Closure;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Recursive array_filter
     *
     * @param array $input
     * @param Closure $closure
     * @return array
     */
    public static function arrayFilterRecursive($input, Closure $closure)
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = self::arrayFilterRecursive($value, $closure);
            }
        }

        return array_filter($input, $closure);
    }
}