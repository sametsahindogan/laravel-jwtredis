<?php

namespace Sametsahindogan\JWTRedis\Services\Result;

class SuccessResult extends Result
{

    /**
     * SuccessResult constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = !empty($data) ? $data : new \stdClass();
        $this->success = true;
    }
}
