<?php
/**
 * libSSE-php
 *
 * Copyright (C) Licson Lee, Tony Yip 2016.
 *
 * Permission is hereby granted, free of charge,
 * to any person obtaining a copy of this software
 * and associated documentation files (the "Software"),
 * to deal in the Software without restriction,
 * including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons
 * to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice
 * shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS",
 * WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category libSSE-php
 * @author   Licson Lee <licson0729@gmail.com>
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/MIT MIT License
 */

namespace Sse;


class Data implements DataInterface
{

    /**
     * @var DataInterface
     */
    private $mechnism;

    /**
     * @var array
     */
    private static $registers = array();

    /**
     * @var bool
     */
    private static $initial = false;

    /**
     * Data constructor.
     *
     * @param string $mechnism the mechnism to use
     * @param array $credinals
     * @return void
     */
    public function __construct($mechnism, array $credinals = array())
    {
        if (!static::$initial)
            static::fireOnInitial();

        if (!array_key_exists($mechnism, static::$registers) || count(static::$registers[$mechnism]) === 0)
            throw new \InvalidArgumentException("$mechnism Mechnism has not been registered");

        $mechnism = static::$registers[$mechnism];
        $this->mechnism = new $mechnism($credinals);
    }

    /**
     * Register Data Mechnism
     *
     * @param string $mechnism
     * @param string $class
     * @return void
     */
    public static function register($mechnism, $class)
    {
        static::$registers[$mechnism] = $class;
    }

    /**
     * Fire the initial job
     */
    public static function fireOnInitial()
    {
        $classes = array(
            'apc'       =>'Sse\\Mechnisms\\ApcMechnism',
            'file'      => 'Sse\\Mechnisms\\FileMechnism',
            'memcache'  => 'Sse\\Mechnisms\\MemcacheMechnism',
            'mongo'     => 'Sse\\Mechnisms\\MongoMechnism',
            'pdo'       => 'Sse\\Mechnisms\\PdoMechnism',
            'redis'     => 'Sse\\Mechnisms\\RedisMechnism',
            'xcache'    => 'Sse\\Mechnisms\\XCacheMechnism',
        );

        foreach ($classes as $class => $mechnism) {
            static::register($class, $mechnism);
        }
    }

    public function get($key)
    {
        return $this->mechnism->get($key);
    }

    public function set($key, $value)
    {
        return $this->mechnism->set($key, $value);
    }

    public function delete($key)
    {
        return $this->mechnism->delete($key);
    }

    public function has($key)
    {
        return $this->mechnism->has($key);
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __unset($name)
    {
        $this->delete($name);
    }

    public function __isset($key)
    {
        return $this->has($key);
    }
}