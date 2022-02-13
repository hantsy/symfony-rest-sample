<?php

namespace App\Dto;

class Greeting
{
    private string $message;

    public function __construct()
    {
    }

    static function of(string $message): Greeting
    {
        $data = new Greeting();
        return $data->setMessage($message);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Greeting
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

}