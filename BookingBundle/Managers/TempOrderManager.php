<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 17:11
 */

namespace EL\BookingBundle\Managers;


use EL\BookingBundle\Entity\TempOrder;
use Symfony\Component\HttpFoundation\Session\Session;

class TempOrderManager
{

    private $session;


    public function __construct(Session $session)
    {
        $this->session   = $session;
    }

    /**
     * each time user test a date for availability this method will be called
     * this method is creating the very first session variable
     * @param $user_date
     * @param $user_n_tickets
     * @param $prefix
     * @return Session
     */
    public function createOrderSession($user_date,$user_n_tickets,$prefix)
    {
        $tempOrder = new TempOrder();
        //hydrate tempOrder object
        //1-setters
        $tempOrder->setTempOrderDate($user_date);
        $tempOrder->setTempNumberOfTickets($user_n_tickets);
        $tempOrder->setTempOrderToken($prefix);
        //2-getters
        $date      = $tempOrder->getTempOrderDate()->format('m-d-Y');
        $n_tickets = $tempOrder->getTempNumberOfTickets();
        $token     = $tempOrder->getTempOrderToken();
        //check if user has already tested a date
        //assign tempOrder object values to session variables
        if(!$this->session->has('sold_out'))
        {
            //create session variables
            $this->session->set('user_date',$date);
            $this->session->set('user_n_tickets',$n_tickets);
            $this->session->set('temp_order_token',$token);
            $this->session->set('sold_out',0);
        }
        else
        {
            //remove and replace session variables
            $this->session->remove('user_date',$date);
            $this->session->remove('user_n_tickets',$n_tickets);
            $this->session->remove('temp_order_token',$token);
            $this->session->set('user_date',$date);
            $this->session->set('user_n_tickets',$n_tickets);
            $this->session->set('temp_order_token',$token);
        }
        return $this->session;
    }

    /**
     * @param $total_booked
     * @param $booking_limit
     * @param $user_date
     * @param $user_n_tickets
     * @param $prefix
     * @return string
     */
    public function checkAvailabilityAndRedirect($total_booked,$booking_limit,$user_date,$user_n_tickets,$prefix)
    {
        if($total_booked < $booking_limit)
        {
            $this->createOrderSession($user_date,$user_n_tickets,$prefix);
            $redirect = 'accueil_billetterie';
        }
        else
        {
            $this->set('sold_out',1);
            $redirect = 'accueil_billetterie';
        }
        return $redirect;
    }

    /**
     * this method is called to destroy all session if a given session is set
     * mainly used to destroy session at the end of shopping process if user goes back to homepage
     * @param $session_name
     */
    public function killSession($session_name)
    {
        if($this->session->has($session_name))
        {
            $this->session->invalidate();
        }
    }

}