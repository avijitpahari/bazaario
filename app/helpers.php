<?php

if (!function_exists('format_inr')) {
    /**
     * Format an amount in Indian Rupee notation (e.g. ₹ 950.00).
     */
    function format_inr(mixed $amount, int $decimals = 2): string
    {
        return '₹' . number_format((float) ($amount ?? 0), $decimals);
    }
}
