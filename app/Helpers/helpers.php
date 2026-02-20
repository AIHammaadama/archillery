<?php

if (! function_exists('highlight')) {
    function highlight(string $text, ?string $search)
    {
        if (! $search) {
            return e($text);
        }

        return preg_replace(
            '/' . preg_quote($search, '/') . '/i',
            '<mark class="bg-warning">$0</mark>',
            e($text)
        );
    }
}