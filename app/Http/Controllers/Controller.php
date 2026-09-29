<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Clean formatted numeric inputs (e.g. currency with Rp or thousand dots)
     * while preserving negative values and native numeric inputs.
     */
    protected function cleanNumericInput(mixed $val): mixed
    {
        if ($val === null || $val === '') {
            return $val;
        }

        if (is_numeric($val)) {
            return $val;
        }

        $str = trim((string) $val);
        $isNegative = str_starts_with($str, '-');
        $cleaned = preg_replace('/[^0-9]/', '', $str);

        if ($cleaned === '') {
            return $val;
        }

        return $isNegative ? "-$cleaned" : $cleaned;
    }
}
