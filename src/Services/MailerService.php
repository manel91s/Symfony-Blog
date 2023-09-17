<?php

namespace App\Services;

use App\Entity\User;
use Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function sendEmail(User $user, array $template): void
    {
        try {

            $email = (new Email())
            ->from('info@manelproyectweb.es')
            ->to($user->getEmail())
            ->subject($template['subject'])
            ->text($template['body']);

            $this->mailer->send($email);
            
        }catch(Exception $e) {
            throw new BadRequestException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        
    }
}
