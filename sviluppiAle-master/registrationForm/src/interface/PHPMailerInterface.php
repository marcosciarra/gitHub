<?php

/**
 * Created by PhpStorm.
 * User: Stagista
 * Date: 25/08/2016
 * Time: 12:03
 */

namespace Amicowin\Interfaccia;

use PHPMailer\PHPMailer\PHPMailer;

class PHPMailerInterface
{

    protected $indirizzoMittente;
    protected $nomeMittente;

    protected $nomeDestinatario;
    protected $idirizzoDestinatario;
    protected $oggettoMail;
    protected $testoMail;
    /** @var PHPMailer */
    protected $mail;


    /**
     * PHPMailerInterface constructor.
     * @param $mail
     */
    public function __construct()
    {
        $this->mail = new PHPMailer();
    }

    /**
     * Costruisce il messaggio
     * @param $idirizzoDestinatario
     * @param $oggettoMail
     * @param $testoMail
     */
    public function creaMessaggio($idirizzoDestinatario, $oggettoMail, $testoMail, $arrayAllegati = array())
    {
        $this->idirizzoDestinatario = $idirizzoDestinatario;
        $this->oggettoMail = $oggettoMail;
        $this->testoMail = $testoMail;
        $this->allegaFile($arrayAllegati);
    }

    public function creaMittente($indirizzoMittente, $nomeMittente = '')
    {
        $this->indirizzoMittente = $indirizzoMittente;
        if ($nomeMittente == '') {
            $this->nomeMittente = $indirizzoMittente;

        } else {
            $this->nomeMittente = $nomeMittente;
        }
    }


    public function inviaMail()
    {

        // use SMTP or use mail()
        if (EMAIL_USE_SMTP) {
            // use SMTP
            $this->mail->IsSMTP();
            // Enable SMTP authentication
            $this->mail->SMTPAuth = EMAIL_SMTP_AUTH;
            // Enable encryption, usually SSL/TLS
            if (defined(EMAIL_SMTP_ENCRYPTION)) {
                $this->mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
            }
            // host server
            $this->mail->Host = EMAIL_SMTP_HOST;
            $this->mail->Username = EMAIL_SMTP_USERNAME;
            $this->mail->Password = EMAIL_SMTP_PASSWORD;
            $this->mail->Port = EMAIL_SMTP_PORT;
        } else {
            $this->mail->IsMail();
        }

        $this->mail->From = $this->indirizzoMittente;
        $this->mail->FromName = $this->nomeMittente;
        $this->mail->AddAddress($this->idirizzoDestinatario);
        $this->mail->Subject = $this->oggettoMail;
        $this->mail->Body = $this->testoMail;


        if (!$this->mail->Send()) {
            return $this->mail->ErrorInfo;
        } else {
            return true;
        }
    }

    public function allegaFile($arrayAllegati)
    {
        if (is_array($arrayAllegati)) {
            foreach ($arrayAllegati as $allegato) {
                $this->mail->AddAttachment($allegato);
            }
        } else {
            $this->mail->AddAttachment($arrayAllegati);
        }
    }

}