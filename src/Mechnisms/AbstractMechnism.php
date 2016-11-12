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

namespace Sse\Mechnisms;

use Sse\DataInterface;

abstract class AbstractMechnism implements DataInterface
{

    /**
     * Seconds of inactive timeout
     * @var int
     */
    protected $lifetime = 6000;

    /**
     * @param array $parameter
     * @return void
     */
    public function __construct(array $parameter)
    {
        if (array_key_exists('gc_lifetime', $parameter)) {
            $this->lifetime = $parameter['gc_lifetime'];
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        $this->delete($name);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $value = $this->get($key);
        return !!$value;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
}