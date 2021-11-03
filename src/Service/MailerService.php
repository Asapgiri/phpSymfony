<?php
namespace App\Service;

use App\Entity\Advertisement;
use App\Entity\PwResetToken;
use App\Entity\User;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailerService implements MailerServiceInterface {
    private MailerInterface $mailer;
    private Address $sender;
    /** @var Address|null */
    private $bcc;

    /**
     * MailerService constructor.
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

        $mail_sender_name = $_ENV['APP_EMAIL_NAME'];
        $mail_sender_email = $_ENV['APP_EMAIL_ADRESS'];
        $this->sender = new Address($mail_sender_email, $mail_sender_name);
    }

    public function sendRegisterEmail(User $user): void
    {
        $this->sendTemplateEmail($user->getEmail(), 'email/register.html.twig', ['user'=>$user]);
    }

    public function sendPwResetEmail(User $user, PwResetToken $pwToken): void
    {
        $this->sendTemplateEmail($user->getEmail(), 'email/pw_token.html.twig', ['user'=>$user, 'pwToken'=>$pwToken]);
    }

    public function sendAdSubmission(Advertisement $ad): void
    {
        $this->setBcc();
        $this->sendTemplateEmail($ad->getAdEmail(), 'email/new_ad.html.twig', ['ad'=>$ad]);
    }

    public function sendTemplateEmail(string $recever, string $twig, array $params, string $subject = "Ne válaszoljon!"): void
    {
        if (!$this->sender) throw new \Exception("Message Sender Not Set Properly!");
        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to(new Address($recever))
            ->subject($subject)
            ->text('')
            ->htmlTemplate($twig)
            ->context($params);
        if ($this->bcc) $email->bcc($this->bcc);

        $this->mailer->send($email);
    }

    public function sendTextEmail(string $recever, string $text, string $subject = "Ne válaszoljon!"): void
    {
        if (!$this->sender) throw new \Exception("Message Sender Not Set Properly!");
        $email = (new Email())
            ->from($this->sender)
            ->to(new Address($recever))
            ->subject($subject)
            ->text($text);
        if ($this->bcc) $email->bcc($this->bcc);

        $this->mailer->send($email);
    }

    private function setBcc() {
        /*try {
            $mail_bcc_name = $_ENV['APP_EMAIL_BCC_NAME'];
        } catch (\Exception $exception) { $mail_bcc_name = null; }
*/
        try {
            $mail_bcc_email = $_ENV['APP_EMAIL_BCC_EMAIL'];
        } catch (\Exception $exception) { $mail_bcc_email = null; }

        if ($mail_bcc_email) $this->bcc = new Address($mail_bcc_email);
        else $this->bcc = $this->sender;
    }

    public function sendHirdetEmail(string $recever, string $twig, array $params, bool $test = false, string $subject = "Hírlevél!"): void
    {
        if (!$this->sender) throw new \Exception("Message Sender Not Set Properly!");
        if ($test) {
            $this->setBcc();
            $recever = $this->bcc;
        }
        else $recever = new Address($recever);
        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to($recever)
            ->subject($subject)
            ->text('')
            ->htmlTemplate($twig)
            ->context($params);

        try {
            $this->mailer->send($email);
        }
        catch (Exception $e) {}
    }
}