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

class FileMechnism extends AbstractMechnism
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param array $arguments
     * @return void
     */
    public function __construct(array $arguments)
    {
        if (!array_key_exists('path', $arguments)) {
            throw new \InvalidArgumentException('Key path does not exists in arguments');
        }
        parent::__construct($arguments);

        $this->path = $arguments['path'];
        if (!is_dir($this->path)) {
            mkdir($this->path);
        }
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $file = $this->getFileName($key);
        if (!file_exists($file)) {
            return null;
        }
        $content = file_get_contents($file);
        $this->gc();
        return $content;
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value)
    {
        $result = file_put_contents($this->path.'/sess_'.sha1($key),$value) === false ? false : true;
        $this->gc();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $path = $this->getFileName($key);
        if(file_exists($path)){
            unlink($path);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        $file = $this->getFileName($key);
        return file_exists($file);
    }

    /**
     * @return void
     */
    private function gc(){
        if($this->lifetime == 0){
            return;
        }
        foreach(glob($this->path . '/sess_*') as $file){
            if(filemtime($file) + $this->lifetime < time() && file_exists($file)){
                unlink($file);
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getFileName($key)
    {
        return $this->path.'/sess_'.sha1($key);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}