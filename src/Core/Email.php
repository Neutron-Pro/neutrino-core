<?php
namespace NeutronStars\Neutrino\Core;

use Exception;
use NeutronStars\Neutrino\Core\View\View;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    private PHPMailer $mailer;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer();
        $this->initMailer();
    }

    /**
     * @throws Exception
     */
    private function initMailer(): void
    {
        $configuration = Kernel::get()->getConfiguration();
        $this->mailer->isSMTP();
        $this->mailer->Host       = $configuration->get('mail.host');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $configuration->get('mail.user');
        $this->mailer->Password   = $configuration->get('mail.password');
        $this->mailer->SMTPSecure = 'ssl';
        $this->mailer->CharSet = $configuration->get('mail.charset');
        $this->mailer->Port       = $configuration->get('mail.port');
        $this->mailer->setFrom($configuration->get('mail.recipientEmail'), $configuration->get('mail.recipientName'));
    }

    /**
     * @param string $to
     * @param string $name
     * @return $this
     * @throws Exception
     */
    public function add(string $to, string $name = ''): self
    {
        $this->mailer->addAddress($to, $name);
        return $this;
    }

    /**
     * @param string $subject
     * @param string $content
     * @param int $engine
     * @param array $params,
     * @param ?string $layout
     * @param bool $isHTML
     * @throws Exception
     */
    public function send(string $subject, string $content, array $params = [], ?string $layout = null, bool $isHTML = true, int $engine = -1): void
    {
        if($engine === -1) {
            $engine = Kernel::get()->getConfiguration()->get('viewEngine');
        }
        $this->mailer->isHTML($isHTML);
        $this->mailer->Subject = $subject;
        if ($isHTML) {
            $this->mailer->Body    = (new View($engine, $content, $params))->run($layout);
        } else {
            $this->mailer->Body    = $content;
        }
        $this->mailer->send();
    }
}
