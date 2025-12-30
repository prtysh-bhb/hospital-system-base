<?php

namespace App\Interfaces;

interface MessageSenderInterface
{
    public function sendMessage(string $to, array $message, string $type = 'text' );
}
