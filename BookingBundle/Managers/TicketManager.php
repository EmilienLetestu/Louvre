<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 17/05/2017
 * Time: 11:17
 */

namespace EL\BookingBundle\Managers;


use Doctrine\ORM\EntityManager;
use EL\BookingBundle\Entity\Billing;
use EL\BookingBundle\Entity\Ticket;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;


class TicketManager
{
    private $session;
    private $doctrine;
    private $request;

    public function __construct(
        Session       $session,
        EntityManager $doctrine,
        RequestStack  $request

    )
    {
        $this->session  = $session;
        $this->doctrine = $doctrine;
        $this->request  = $request;
    }

    /**
     * @param $name
     * @param $surname
     * @param $dob
     * @param $discount
     * @param $time_access
     * @return mixed
     */
    public function createSession($name,$surname,$dob,$discount,$time_access)
    {
        //fetch date and order_token into session
        $date = $this->session->get('user_date');
        $order_token = $this->session->get('temp_order_token');
        //initialise requested classes
        $ticket = new Ticket();
        $tools = new Tools();
        $billing = new Billing();
        //->price  : age + discount + museum time access => ticket price
        $age = $tools->getAge($dob);
        $price = $tools->getPriceRange($age, $discount);
        $ticket_price = $tools->getTicketPrice($time_access, $price);
        $ticket->setDate($date)->getDate();
        $ticket->setName($name)->getName();
        $ticket->setSurname($surname)->getSurname();
        $ticket->setDob($dob)->getDob();
        $ticket->setDiscount($discount)->getDiscount();
        $ticket->setToken($name, $surname)->getToken();
        $ticket->setTimeAccess($time_access)->getTimeAccess($display = true);
        $ticket->setPrice($ticket_price)->getPrice();
        $ticket->setPriceType($dob);
        $ticket->setTimeAccessType($time_access)->getTimeAccessType();
        $ticket->setOrderToken($order_token);
        //add ticket to billing
        $billing->addTicket($ticket);
        

        return $this->session->get('order');
    }

    /**
     * @param $order
     * @return array
     */
    public function buildOrder($order)
    {
        $number_of_tickets = count($order);
        $total = 0;

        foreach ($order as $ticket)
        {
            $total += $ticket->getPrice();
        }
        $order = array('total'             => $total,
                       'number_of_tickets' => $number_of_tickets
        );

        $this->session->set('total',$order['total']);
        $this->session->set('tickets',$order['number_of_tickets']);

        return $order;
    }

    /**
     * @param bool $save
     * @return mixed
     */
    public function getTickets($save = false)
    {
        //initialise requested classes
        $tools  = new Tools();
        //fetch order, order_token, and date into session
        $order_token = $this->session->get('temp_order_token');
        $order       = $this->session->get('order');
        $date        = $this->session->get('user_date');
        //get date from session and turn it into a "datetime format"
        $date_time = $tools->formatDate($date);
        foreach ($order as $ticket)
        {
            $ticket->setDate(\DateTime::createFromFormat('m-d-Y H:i:s',$date_time));
            $ticket->getName();
            $ticket->getSurname();
            $ticket->getDiscount();
            $ticket->getPriceType();
            $ticket->setOrderToken($order_token);
            $ticket->getTimeAccess();
            $ticket->getPrice();
            $ticket->getDob();
            if($save == true) {$this->doctrine->persist($ticket);};
        }
        return $ticket;
    }

    /**
     * @param $query
     * @param $session_name
     * @return mixed
     */
    public function deleteTicketFromOrderInProgress($query,$session_name)
    {
       $id = $this->request->getCurrentRequest()->query->get($query);
       $order = $this->session->get($session_name);
       unset($order[$id]);
       unset($_SESSION[$session_name][$id]);
       $this->session->set($session_name,$order);
       $updated_session = $this->session->get($session_name);

       $this->buildOrder($updated_session);
       return $updated_session;
    }

    /**
     * @param $query
     * @param $session_name
     * @return mixed
     */
    public function getTicketToModify($query,$session_name)
    {
        $id = $this->request->getCurrentRequest()->query->get($query);
        $order = $this->session->get($session_name);
        $ticket_to_modify = $order[$id];
        return $ticket_to_modify;
    }

    /**
     * @param $query
     * @param $session_name
     * @param $name
     * @param $surname
     * @param $dob
     * @param $discount
     * @param $time_access
     * @return mixed
     */
    public function modifyTicket($query,$session_name,$name,$surname,$dob,$discount,$time_access)
    {
        //fetch date and order_token into session
        $date = $this->session->get('user_date');
        $order_token = $this->session->get('temp_order_token');
        //initialise requested classes
        $ticket = new Ticket();
        $tools  = new Tools();
        //->price type : age + discount + museum time access => ticket price
        $age   = $tools->getAge($dob);
        $price = $tools->getPriceRange($age,$discount);
        $ticket_price = $tools->getTicketPrice($time_access,$price);
        $ticket->setDate($date)->getDate();
        $ticket->setName($name)->getName();
        $ticket->setSurname($surname)->getSurname();
        $ticket->setDob($dob)->getDob();
        $ticket->setDiscount($discount)->getDiscount();
        $ticket->setToken($name,$surname)->getToken();
        $ticket->setTimeAccess($time_access)->getTimeAccess($display = true);
        $ticket->setPrice($ticket_price)->getPrice();
        $ticket->setPriceType($dob);
        $ticket->setTimeAccessType($time_access)->getTimeAccessType();
        $ticket->setOrderToken($order_token);

        $id = $this->request->getCurrentRequest()->query->get($query);
        $order = $this->session->get($session_name);
        $order[$id] = $ticket;
        $this->session->set($session_name,$order);
        $updated_session = $this->session->get($session_name);

        $this->buildOrder($updated_session);

        return $updated_session;
    }

}