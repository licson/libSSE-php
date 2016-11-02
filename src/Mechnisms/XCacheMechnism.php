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


class XCacheMechnism extends AbstractMechnism
{

    /**
     * @var int
     */
    protected $lifetime = 0;

    /**
     * @param array $parameter
     * @return void
     */
    public function __construct(array $parameter)
    {
        if (!extension_loaded('xcache')) {
            throw new \RuntimeException('XCache is not enabled, Unable to use XCacheMechnism');
        }
        parent::__construct($parameter);
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return xcache_isset($key);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        return xcache_set($key, $value, $this->lifetime);
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return xcache_get($key);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }
}