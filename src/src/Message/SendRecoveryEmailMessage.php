<?php

namespace App\Message;

final class SendRecoveryEmailMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

    private $mailTo;
    private $resetToken;
    private $tokenLifetime;

    public function __construct(string $mailTo, $resetToken, string $tokenLifetime)
    {
        $this->mailTo = $mailTo;
        $this->resetToken = $resetToken;
        $this->tokenLifetime = $tokenLifetime;
    }
    
    public function getMailTo(): string
    {
        return $this->mailTo;
    }

    public function getResetToken()
    {
        return $this->resetToken;
    }
   
    public function getTokenLifetime(): string
    {
        return $this->tokenLifetime;
    }
}
