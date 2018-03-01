<?php

declare(strict_types=1);

namespace Application\Stream;

use Zend\Diactoros\Stream;

/**
 * This is a stream that will delete the temporary file on disk when not needed anymore.
 *
 * Its usage is primarily to serve temporary file to the browser.
 */
class TemporaryFile extends Stream
{
    public function __destruct()
    {
        $this->close();
        @unlink($this->stream);
    }
}
