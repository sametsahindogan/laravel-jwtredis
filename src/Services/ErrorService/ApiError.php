<?php

namespace Sametsahindogan\JWTRedis\Services\ErrorService;


class ApiError
{
    protected $message;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var array
     */
    protected $extra;


    /**
     * ApiError constructor.
     *
     * @param string $message
     * @param string $title
     * @param int    $code
     * @param array  $extra
     */
    public function __construct(String $message, String $title = '', int $code = 0, array $extra = []) {


        $this->message = $message;
        $this->title = $title;
        $this->code = $code;
        $this->extra = $extra;
    }

    /**
     * @return String
     */
    public function getMessage(): String
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }


}
