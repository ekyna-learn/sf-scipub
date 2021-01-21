<?php

namespace App\Service;

use App\Entity\Publication;
use Twig\Environment;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Notifier
{
    /** @var MailerInterface  */
    private $mailer;

    /** @var Environment */
    private $twig;

    /** @var string */
    private $senderEmail;

    /** @var string */
    private $recipientEmail;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        string $senderEmail,
        string $recipientEmail
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->senderEmail = $senderEmail;
        $this->recipientEmail = $recipientEmail;
    }

    public function notify(Publication $publication): bool
    {
        $content = $this
            ->twig
            ->render('Notify/publication.html.twig', [
                'publication' => $publication,
            ]);

        $email = (new Email())
            ->from($this->senderEmail)
            ->to($this->recipientEmail)
            ->subject('Nouvelle publication')
            //->text()         // Version texte brut
            ->html($content);  // Version html

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return false;
        }

        return true;
    }
}
