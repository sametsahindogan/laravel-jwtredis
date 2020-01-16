<?php
namespace Sametsahindogan\JWTRedis\Services\Result;


class Result
{

    /** @var bool $success */
    public $success = true;

    /** @var int $status_code */
    protected $status_code = 200;

    /** @var array $data */
    public $data = [];

    public function getStatusCode(): int
    {
        return $this->status_code;
    }
}
