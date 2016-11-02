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

use MongoClient;
use MongoDate;

class MongoMechnism extends AbstractMechnism
{

    /**
     * @var MongoClient
     */
    private $connection;

    /**
     * @var \MongoCollection
     */
    private $collection;

    /**
     * @var array
     */
    private $options;

    /**
     * @param array $parameter
     * @return void
     */
    public function __construct(array $parameter)
    {
        parent::__construct($parameter);
        $parameter['option'] = array_merge(array(
            "connect" => TRUE
        ), $parameter['option'] ? : array());
        $this->connection = new MongoClient($parameter['server'], $parameter['option']);
        $this->options = array_merge(array(
            'id_field' => '_id',
            'data_field' => 'data',
            'time_field' => 'time',
            'expiry_field' => 'expires_at',
        ), $parameter['parameter']);
    }

    /**
     * @return \MongoCollection
     */
    protected function getCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->connection->selectCollection($this->options['database'], $this->options['collection']);
        }

        return $this->collection;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $data = $this->getCollection()->findOne(array(
            $this->options['id_fields'] => $key,
            $this->options['expiry_field'] => array('$gte' => new MongoDate())
        ));

        return null === $data ? '' : $data[$this->options['data_field']];
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $expire = new MongoDate(time() + intval($this->lifetime));

        $fields = array(
            $this->options['data_field'] => $value,
            $this->options['time_field'] => new MongoDate(),
            $this->options['expiry_field'] => $expire,
        );

        $this->getCollection()->update(
            array($this->options['id_field'] => $key),
            array('$set' => $fields),
            array('upsert' => true, 'multiple' => false)
        );
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->getCollection()->remove(array(
            $this->options['id_field'] => $key
        ));

        return true;
    }
}