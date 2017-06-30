<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 23/05/2017
 * Time: 10:07
 */

namespace EL\BookingBundle\Services;


use Doctrine\ORM\EntityManager;
use EL\BookingBundle\Managers\Tools;
use Symfony\Component\HttpFoundation\Session\Session;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;

class Mail
{
    private $doctrine;
    private $mailer;
    private $templating;
    private $session;

    public function __construct(
        EntityManager      $doctrine,
        \Swift_Mailer      $mailer,
        \Twig_Environment  $templating,
        Session            $session
    )
    {
        $this->doctrine    = $doctrine;
        $this->mailer      = $mailer;
        $this->templating  = $templating;
        $this->session     = $session;
    }

    /**
     * @param $email
     */
    public function sendMail($email)
    {
        $billing = $this->session->get('billing');
        //get user tickets
        $repository  = $this->doctrine->getRepository('ELBookingBundle:Ticket');
        $order_token = $billing->getToken();
        $ticket_list = $repository->findBy(['orderToken'=>$order_token]);
        foreach ($ticket_list as $ticket)
        {
            $ticket->getName();
            $ticket->getSurname();
            $ticket->getDiscount();
            $ticket->getOrderToken();
            $ticket->getDate()->format('d-m-Y');
            $ticket->getTimeAccess();
            $ticket->getDob();
            $ticket->getPrice();
            $ticket->setPriceType($ticket->getDob());
            $ticket->getPriceType();
            $ticket->getTimeAccessType();
        }
        $this->generateQrCode($order_token,$billing->getId());
        $visit_day = $billing->getVisitDay()->format('d-m-Y');
        //create mail
        $message = \Swift_Message::newInstance();
        $code = 'qrcode'.$billing->getId().'.png';
        $logo    = $message->embed(\Swift_Image::fromPath('../web/images/logo_pyramide_accueil.png'));
        $qr_code = $message->embed(\Swift_Image::fromPath('../web/Qrcodes/'.$code));
        $message
            ->setSubject('Votre commande de billets d\'entrée au Musée du Louvre')
            ->setFrom('billetterie_louvre@gmail.com')
            ->setTo($email)
            ->setBody($this->templating->Render('ELBookingBundle:Email:eticket.html.twig',[
                'billing'     => $billing,
                'visit_day'   => $visit_day,
                'ticket_list' => $ticket_list,
                'image_logo'  => $logo,
                'qr_code'     => $qr_code
            ]),'text/html');

        //send mail
        $this->mailer->send($message);
        //will be used later on to detect end of process and kill session
        $this->session->set('mail_sent',1);
    }

    public function generateQrCode($order_token,$billing_id)
    {
        $qr_code = new QrCode();
        $qr_code
            ->setText($order_token)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setLabel('Présenter ce code à l\'accueil')
            ->setLogoPath('../web/images/logo_pyramide_accueil.png')
        ;
        $code = 'qrcode'.$billing_id.'.png';
        $qr_code->writeFile('../web/Qrcodes/'.$code);

    }
}