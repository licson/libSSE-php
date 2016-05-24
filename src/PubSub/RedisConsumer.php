<?php
/**
 * libSSE-php
 *
 * Copyright (C) Tony Yip 2016.
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
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/MIT MIT License
 */

namespace Sse\PubSub;


use Predis\Client;

class RedisConsumer implements ConsumerInterface
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $pubsub = [];

    /**
     * @var array
     */
    private $keepOn = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function subscribe($channel, \Closure $callback)
    {
        $pubsub = $this->client->pubSubLoop();

        $pubsub->subscribe($channel);

        $this->pubsub[$channel] = $pubsub;
        $this->keepOn[] = $channel;

        foreach ($pubsub as $message) {
            if ($message->kind == 'message' && $message->channel == 'control_channel') {
                if (in_array($channel, $this->keepOn))
                    $callback($message->payload);
                else
                    $pubsub->unsubscribe();
            }
        }
    }

    public function unsubscribe($channel)
    {
        if (($key = array_search($channel, $this->keepOn) !== false))
            unset($this->keepOn[$key]);
    }
}