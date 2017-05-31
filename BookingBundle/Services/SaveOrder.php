<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 22/05/2017
 * Time: 11:28
 */

namespace EL\BookingBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\BookingBundle\Entity\Billing;
use EL\BookingBundle\Managers\TicketManager;
use EL\BookingBundle\Managers\Tools;
use Symfony\Component\HttpFoundation\Session\Session;
class SaveOrder
{

    private $doctrine;
    private $session;
    private $ticketManager;

    /**
     * SaveOrder constructor.
     * @param EntityManager $doctrine
     * @param Session $session
     * @param TicketManager $ticketManager
     */
    public function __construct(
        EntityManager $doctrine,
        Session $session,
        TicketManager $ticketManager
    )
    {
        $this->doctrine = $doctrine;
        $this->session  = $session;
        $this->ticketManager = $ticketManager;
    }

    /**
     * @param $email
     * @param $name
     * @param $surname
     * @param $source
     */
    public function saveOrder($email,$name,$surname,$source)
    {
        //initialise classes en dependencies
        $billing = new Billing();
        $tools   = new Tools();
        $em = $this->doctrine;
        //fetch order token into session
        $order_token = $this->session->get('temp_order_token');
        $date = $this->session->get('user_date');

        //get date from session and turn it into a "datetime format"
        $date_time = $tools->formatDate($date);

        //1 save billing
        //1-a hydrate billing
        $billing->setEmail($email);
        $billing->setName($name);
        $billing->setSurname($surname);
        $billing->setNumberOfTickets($this->session->get('tickets'));
        $billing->setVisitDay(\DateTime::createFromFormat('m-d-Y H:i:s',$date_time));
        $billing->setToken($order_token);
        $billing->setStripeToken($source);
        $billing->setPrice($this->session->get('total'));
        //1-b persist it
        $em->persist($billing);

        //2 get user tickets for saving
        $this->ticketManager->getTickets($billing,$save = true);

        //3-store ticket and billing into db
        $em->flush();

        $this->session->set('billing',$billing);
    }


}