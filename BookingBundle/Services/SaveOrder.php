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
use EL\BookingBundle\Managers\Tools;
use Symfony\Component\HttpFoundation\Session\Session;
class SaveOrder
{
    private $doctrine;
    private $session;
    private $tools;

    /**
     * SaveOrder constructor.
     * @param EntityManager $doctrine
     * @param Session $session
     * @param Tools   $tools
     */
    public function __construct(
        EntityManager $doctrine,
        Session       $session,
        Tools         $tools
    )
    {
        $this->doctrine = $doctrine;
        $this->session  = $session;
        $this->tools    = $tools;
    }

    /**
     * this method will fetch and persist all ticket inside cart
     * @param $billing
     * @return mixed
     */
    public function getAndSaveTickets($billing)
    {
        //fetch order, order_token, and date into session
        $order_token = $this->session->get('temp_order_token');
        $order = $this->session->get('order');
        $date = $this->session->get('user_date');
        //get date from session and turn it into a "datetime format"
        $date_time = $this->tools->formatDate($date);
        foreach ($order as $key)
        {
            foreach ($key as $ticket)
            {
                $ticket->setDate(\DateTime::createFromFormat('d-m-Y H:i:s', $date_time));
                $ticket->getName();
                $ticket->getSurname();
                $ticket->getDiscount();
                $ticket->getPriceType();
                $ticket->setOrderToken($order_token);
                $ticket->getTimeAccess();
                $ticket->getPrice();
                $ticket->getDob();
                $ticket->setBilling($billing);
                $this->doctrine->persist($ticket);
            }
        }
        return $ticket;
    }

    /**
     * get all data and store them into db
     * @param $email
     * @param $name
     * @param $surname
     */
    public function saveOrder($email,$name,$surname)
    {
        //initialise classes en dependencies
        $billing = new Billing();
        $em = $this->doctrine;
        //fetch order token into session
        $order_token = $this->session->get('temp_order_token');
        $date = $this->session->get('user_date');
        //get date from session and turn it into a "datetime format"
        $date_time = $this->tools->formatDate($date);
        //1 save billing
        //1-a hydrate billing
        $billing->setEmail($email);
        $billing->setName($name);
        $billing->setSurname($surname);
        $billing->setNumberOfTickets($this->session->get('tickets'));
        $billing->setVisitDay(\DateTime::createFromFormat('d-m-Y H:i:s',$date_time));
        $billing->setToken($order_token);
        $billing->setStripeToken($this->session->get('customer'));
        $billing->setPrice($this->session->get('total'));
        //2 get user tickets for saving
        $this->getAndSaveTickets($billing);
        //2b persist it
        $em->persist($billing);
        //3-store ticket and billing into db
        $em->flush();
        //store billing into session => will be use later on to build email
        $this->session->set('billing',$billing);
    }
}