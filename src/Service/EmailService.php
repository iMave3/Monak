<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmailService
{
    public function __construct(private MailerInterface $mailer, private ValidatorInterface $validator)
    {
    }

    public function sendEmail(string $emailTo, string $subject, string $template, array $context) : void
    {
        $mail = (new TemplatedEmail())
            ->from(new Address('no-reply@monak.cz', 'Monak Email Bot'))
            ->to($emailTo)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context)
        ;

        $this->mailer->send($mail);
    }

    public function isFaultyEmail(string $email) : bool
    {
        $errors = $this->validator->validate($email, [
            new Email()
        ]);

        return count($errors) !== 0;
    }
}
