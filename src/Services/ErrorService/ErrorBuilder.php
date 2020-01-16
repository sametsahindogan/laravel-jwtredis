<?php

namespace Sametsahindogan\JWTRedis\Services\ErrorService;

class ErrorBuilder
{

    protected $message = '';
    protected $title = '';
    protected $code = 0;
    protected $extra = [];

    public function message(String $message): ErrorBuilder
    {
        $this->message = trim($message);
        return $this;
    }

    public function title(String $title): ErrorBuilder
    {
        $this->title = trim($title);
        return $this;
    }

    public function code(int $code): ErrorBuilder
    {
        $this->code = $code;
        return $this;
    }

    public function extra(array $extra): ErrorBuilder
    {
        $this->extra = $extra;
        return $this;
    }

    private function validate(): bool
    {

        if('' === $this->message){

            return false;
        }

        return true;
    }

    public function buildAsArray(): array
    {

        if(!$this->validate()){

            return [];
        }

        return [
            'message' => $this->message,
            'title' => $this->title,
            'code' => $this->code,
            'extra' => $this->extra,
        ];
    }

    /**
     * @return ApiError|false
     */
    public function buildAsObject()
    {

        if(!$this->validate()){

            return false;
        }

        return new ApiError($this->message, $this->title, $this->code, $this->extra);
    }
}
