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
    public function sendEmail(User $user): void
    {
        try {

            $email = (new Email())
            ->from('info@manelproyectweb.es')
            ->to($user->getEmail())
            ->subject('Información de activación de cuenta')
            ->text($this->getBodyEmail($user));

            $this->mailer->send($email);  
            
        }catch(Exception $e) {
            throw new BadRequestException($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        
    }

    private function getBodyEmail(User $user): string
    {
        if(array_key_exists('HTTP_HOST', $_SERVER)) {
            return 'Bievenido a la web de Manel, para completar el registro de tu cuenta, haz click en el siguiente enlace: http://' . $_SERVER['HTTP_HOST'] . '/user/activate/' . $user->getToken();
        }
        return "Este es el email para enviar cuando se lanzan los tests";
    }
}
