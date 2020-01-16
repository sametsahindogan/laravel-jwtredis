<?php

namespace Sametsahindogan\JWTRedis\Services\Result;

use Sametsahindogan\JWTRedis\Services\ErrorService\ErrorBuilder;

/**
 * Class ErrorResult
 * @package App\Services\Result
 */
class ErrorResult extends Result
{

    public $success = false;

    /**
     * ErrorResult constructor.
     * @param ErrorBuilder $error_builder
     * @param int $status_code
     */
    public function __construct(ErrorBuilder $error_builder, int $status_code = 400)
    {

        $this->data = $error_builder->buildAsArray();
        $this->status_code = $status_code;
    }
}
