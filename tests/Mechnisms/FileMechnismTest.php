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

namespace Sse\Tests\Mechnisms;


use Sse\Mechnisms\FileMechnism;

class FileMechnismTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $storage = new FileMechnism(array(
            'path' => '/tmp/sse'
        ));

        $this->assertInstanceOf('Sse\\Mechnisms\\FileMechnism', $storage);

        rmdir('/tmp/sse');
    }

    public function testSavePath()
    {
        $storage = new FileMechnism(array(
            'path' => '/tmp/sse'
        ));

        $this->assertEquals('/tmp/sse', $storage->getPath());
        $this->assertTrue(is_dir(realpath($storage->getPath())));

        rmdir('/tmp/sse');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructException()
    {
        $storage = new FileMechnism(array());
    }
}
