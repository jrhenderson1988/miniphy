<?php

namespace Miniphy\Helpers;

class StringHelper
{
    /**
     * Generate a random string. On systems where random_int is available (PHP 7+ or PHP 5.x with
     * paragonie/random_compat installed) it is used to generate more cryptographically secure random integers from the
     * alphabet. If random_int is not available, the rand function is used instead.
     *
     * @param int    $length
     * @param string $alphabet
     *
     * @return string
     */
    public function random($length = 12, $alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $random = '';
        $randomInt = is_callable('random_int') ? 'random_int' : 'rand';

        for ($i = 0, $max = mb_strlen($alphabet) - 1; $i < $length; $i++) {
            $random .= $alphabet[$randomInt(0, $max)];
        }

        return $random;
    }

    /**
     * Convert the provided string to StudlyCase. All hyphens and underscores are converted to spaces, each word is
     * converted to Proper Case in that each leading letter is converted to uppercase, and the spaces are removed.
     *
     * @param string $string
     *
     * @return string
     */
    public function studly($string)
    {
        $string = ucwords(str_replace(['-', '_'], ' ', $string));

        return str_replace(' ', '', $string);
    }
}
