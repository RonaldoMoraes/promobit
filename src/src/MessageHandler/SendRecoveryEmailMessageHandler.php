<?php

namespace App\MessageHandler;

use App\Message\SendRecoveryEmailMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class SendRecoveryEmailMessageHandler implements MessageHandlerInterface
{
    private $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        
    }
    public function __invoke(SendRecoveryEmailMessage $message)
    {
        // sleep(15);
        $email = (new TemplatedEmail())
            ->from(new Address('ronaldo.moraes1990@gmail.com', 'Acme Mail Bot'))
            ->to($message->getMailTo())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $message->getResetToken(),
                'tokenLifetime' => $message->getTokenLifetime(),
            ])
        ;

        $this->mailer->send($email);
    }
}
