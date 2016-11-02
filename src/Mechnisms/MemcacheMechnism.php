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

use Memcached;

class MemcacheMechnism extends AbstractMechnism
{
    /**
     * @var Memcached
     */
    private $connection;

    /**
     * @var int
     */
    protected $lifetime = 0;

    public function __construct(array $parameter)
    {
        parent::__construct($parameter);
        $this->connection = isset($parameter['memcache_id']) ? new Memcached($parameter['memcache_id']) : new Memcached;
        if (isset($parameter['server'])) {
            $this->connection->addServers($parameter['server']);
        }
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->connection->get($key);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $this->connection->set($key, $value, $this->lifetime ? $this->lifetime + time() : 0);
        return $this->connection->getResultCode() === Memcached::RES_STORED;
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->connection->delete($key);
        return $this->connection->getResultCode() === Memcached::RES_DELETED;
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return parent::has($key) && $this->connection->getResultCode() === Memcached::RES_SUCCESS;
    }
}