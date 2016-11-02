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

namespace Sse\Tests;

use Sse\SSE;
use Symfony\Component\HttpFoundation\Request;

class SSETest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $sse = new SSE();
        $this->assertEquals(0.5, $sse->sleep_time);
        $this->assertEquals(600, $sse->exec_limit);
        $this->assertEquals(true, $sse->client_reconnect);
        $this->assertEquals(false, $sse->allow_cors);
        $this->assertEquals(300, $sse->keep_alive_time);
        $this->assertEquals(false, $sse->is_reconnect);
        $this->assertEquals(false, $sse->use_chunked_encoding);

        $request = Request::create('/', 'GET', array(), array(), array(), array('HTTP_LAST_EVENT_ID' => 5));
        $sse = new SSE($request);
        $this->assertAttributeEquals(5, 'id', $sse);
        $this->assertTrue($sse->is_reconnect);
    }

    public function testCreateResponse()
    {
        $sse = new SSE();
        $response = $sse->createResponse();

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\StreamedResponse', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('text/event-stream', $response->headers->get('Content-Type'));

        $sse->allow_cors = true;
        $sse->use_chunked_encoding = true;
        $response = $sse->createResponse();
        $this->assertEquals('*', $response->headers->get('Access-Control-Allow-Origin'));
        $this->assertEquals('true', $response->headers->get('Access-Control-Allow-Credentials'));
        $this->assertEquals('chunked', $response->headers->get('Transfer-encoding'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPreventIsReconnectSet()
    {
        $sse = new SSE();
        $sse->set('is_reconnect', true);
    }

    public function testStart()
    {
        $sse = new SSE();
        $sse->start();
    }

    public function testHasEventListener()
    {
        $sse = new SSE();
        $this->assertFalse($sse->hasEventListener());
    }

    public function testGetEventListener()
    {
        $sse = new SSE();
        $this->assertEquals(array(), $sse->getEventListeners());
    }

    public function testSetStart()
    {
        $sse = new SSE();
        $sse->setStart(123456);
        $this->assertAttributeEquals(123456, 'start', $sse);
    }

    public function testGetStart()
    {
        $sse = new SSE();
        $sse->setStart(123456);
        $this->assertEquals(123456, $sse->getStart());
    }
}

