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

abstract class Utils
{
    /**
     * Make strings SSE friendly (For internal use only)
     *
     * @param string $string data to be processed
     * @return string
     *
     * @deprecated Please use SSE::wrapData to replace
     */
    public static function sseData($string)
    {
        return 'data:' . str_replace("\n","\ndata: ",$string);
    }

    /**
     * For output a SSE data block (For internal use only)
     *
     * @param mixed $id Event ID
     * @param string $data Event Data
     * @param string $name Event Name
     *
     * @deprecated Please use SSE::sendBlock to replace
     */
    public static function sseBlock($id, $data, $name = null)
    {
        static::sseSend("id: $id\n");
        if (strlen($name) && $name !== null) {
            static::sseSend("event: $name\n");
        }

        static::sseSend(static::sseData($data) . "\n\n");
    }

    /**
     * @param string $content
     * @deprecated Please use SSE::send to replace
     */
    public static function sseSend($content)
    {
        print($content);
    }

    /**
     * Calculate the modulus of time
     *
     * @param int $start
     * @param int $interval
     *
     * @return int
     */
    public static function timeMod($start, $interval)
    {
        return static::timeDiff($start) % $interval;
    }

    /**
     * Calculate the time difference
     *
     * @param int $start
     * @return int
     */
    public static function timeDiff($start)
    {
        return time() - $start;
    }
}
