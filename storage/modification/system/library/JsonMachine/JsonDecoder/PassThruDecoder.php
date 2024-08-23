<?php
/* This file is under Git Control by KDSI. */

declare(strict_types=1);

namespace JsonMachine\JsonDecoder;

class PassThruDecoder implements ItemDecoder
{
    public function decode($jsonValue)
    {
        return new ValidResult($jsonValue);
    }
}
