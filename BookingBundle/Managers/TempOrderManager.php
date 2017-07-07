<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 17:11
 */

namespace EL\BookingBundle\Managers;

use EL\BookingBundle\Entity\TempOrder;
use EL\BookingBundle\Form\CheckStatusType;
use EL\BookingBundle\Services\MuseumPolicy;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class TempOrderManager
{

    private $session;
    private $formFactory;
    private $policy;

    public function __construct
    (
        Session         $session,
        FormFactory     $formFactory,
        MuseumPolicy    $policy,
        Tools           $tools
    )
    {
        $this->session      = $session;
        $this->formFactory  = $formFactory;
        $this->policy       = $policy;
        $this->tools        = $tools;
    }

    public function checkStatusAndProcess(Request $request,$timezone,$pm_access,$booking_limit,$prefix)
    {
        //end session if user as already bought his tickets
        $temp_order = new TempOrder();
        $disclaimer = $this->tools->getDisclaimer(
            $timezone,
            $pm_access
        );
        //create form
        $booking_status_form = $this->formFactory->create(CheckStatusType::class,$temp_order);
        //processing form
        $booking_status_form->handleRequest($request);
        if($booking_status_form->isSubmitted()&&$booking_status_form->isValid())
        {
            $this->killSession("payment_success");
            $this->session->remove('tickets');
            //extract data
            $date = $booking_status_form->get('temp_order_date')->getData();
            $tickets = $booking_status_form->get('temp_number_of_tickets')->getData();
            //check booking for requested date
            $total_booked = $this->policy->getTotalBooked(
                $date,
                $tickets
            );
            //compile all data and results => set session var
            $this->checkAvailabilityAndRedirect(
                $total_booked,
                $booking_limit,
                $date,$tickets,
                $prefix
            );
        }
        //prepare data to render in view
        $render = [$booking_status_form->createView(),$disclaimer];
        return $render;
    }

    /**
     * each time user test a date for availability this method will be called
     * this method is creating the very first session variable
     * @param $user_date
     * @param $user_n_tickets
     * @param $prefix
     * @return Session
     */
    public function createTempOrderSession($user_date,$user_n_tickets,$prefix)
    {
        $tempOrder = new TempOrder();
        //hydrate tempOrder object
        //1-setters
        $tempOrder->setTempOrderDate($user_date);
        $tempOrder->setTempNumberOfTickets($user_n_tickets);
        $tempOrder->setTempOrderToken($prefix);
        //2-getters
        $date      = $tempOrder->getTempOrderDate()->format('d-m-Y');
        $n_tickets = $tempOrder->getTempNumberOfTickets();
        $token     = $tempOrder->getTempOrderToken();
        //check if user has already tested a date
        //assign tempOrder object values to session variables
        if(!$this->session->has('sold_out'))
        {
            //create session variables
            $this->session->start();
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
            $this->session->set('sold_out',0);
            $this->session->remove('order');
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
            $this->createTempOrderSession($user_date,$user_n_tickets,$prefix);
        }
        else
        {
            $this->session->set('sold_out',1);
        }
        return $this;
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