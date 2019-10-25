<?php

namespace Neat\Object\Test\Helper;

use PHPUnit\Framework\Constraint\Callback;

/**
 * @method void assertEquals($expected, $actual)
 * @method Callback callback(callable $callback)
 */
trait SQLHelper
{
    /**
     * Minify SQL query by removing unused whitespace
     *
     * @param string $query
     * @return string
     */
    protected function minifySQL($query)
    {
        $replace = [
            '|\s+|m'     => ' ',
            '|\s*,\s*|m' => ',',
            '|\s*=\s*|m' => '=',
        ];

        return preg_replace(array_keys($replace), $replace, $query);
    }

    /**
     * Assert SQL matches expectation
     *
     * Normalizes whitespace to make the tests less fragile
     *
     * @param string $expected
     * @param string $actual
     */
    protected function assertSQL($expected, $actual)
    {
        $this->assertEquals(
            $this->minifySQL($expected),
            $this->minifySQL($actual)
        );
    }

    /**
     * SQL expectation constraint
     *
     * @param string $expected
     * @return callable|Callback
     */
    protected function sql($expected)
    {
        return $this->callback(function ($query) use ($expected) {
            return $this->minifySQL($query) == $this->minifySQL($expected);
        });
    }
}
