<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 17/05/2017
 * Time: 11:17
 */

namespace EL\BookingBundle\Managers;
use Doctrine\ORM\EntityManager;
use EL\BookingBundle\Entity\Ticket;
use EL\BookingBundle\Form\TicketType;
use EL\BookingBundle\Services\MuseumPolicy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class TicketManager
{
    private $session;
    private $doctrine;
    private $requestStack;
    private $policy;
    private $tools;
    private $formFactory;

    public function __construct(
        Session $session,
        EntityManager $doctrine,
        RequestStack $requestStack,
        MuseumPolicy $policy,
        Tools $tools,
        FormFactory $formFactory
    )
    {
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
        $this->policy = $policy;
        $this->tools = $tools;
        $this->formFactory = $formFactory;
    }

    /**
     * get user submitted data(form)
     * get some session stored data
     * then prepare a new ticket which will be added to cart later on with another method
     * @param $name
     * @param $surname
     * @param $dob
     * @param $discount
     * @param $time_access
     * @return mixed
     */
    public function createOrder($name, $surname, $dob, $discount, $time_access)
    {
        //fetch date and order_token into session
        $date = $this->session->get('user_date');
        $order_token = $this->session->get('temp_order_token');
        //initialise requested classes
        $ticket = new Ticket();
        //->price  : age + discount + museum time access => ticket price
        $age = $this->tools->getAge($dob);
        $price = $this->tools->getPriceRange($age,$discount);
        $ticket_price = $this->tools->getTicketPrice($time_access, $price);
        $ticket->setDate($date)->getDate();
        $ticket->setName($name)->getName();
        $ticket->setSurname($surname)->getSurname();
        $ticket->setDob($dob)->getDob();
        $ticket->setDiscount($discount)->getDiscount();
        $ticket->setToken($name, $surname)->getToken();
        $ticket->setTimeAccess($time_access)->getTimeAccess($display = true);
        $ticket->setPrice($ticket_price)->getPrice();
        $ticket->setPriceType($dob,$discount);
        $ticket->setTimeAccessType($time_access)->getTimeAccessType();
        $ticket->setOrderToken($order_token);
        return $ticket;
    }

    /**
     * call the method at the very beginning of shopping process to create a cart
     * if cart hasn't been created yet => create it
     * @return mixed
     */
    public function isSessionSet()
    {
        if (!$this->session->has('order')) {
            $this->session->set('order', array());
        }

        return $this->session->get('order');
    }

    /**
     * add any given new ticket to cart
     * control if a daily booking limit is reached
     * @param $ticket
     * @return mixed|void
     */
    public function addToOrder($ticket)
    {
        if($this->session->get('tickets_sold') < 1000)
        {
            $order = $this->isSessionSet();
            $order[] = array($ticket);
            $this->buildOrder($order);
            $order_in_progress = $this->session->set('order', $order);
        }
        else
        {
            $order_in_progress = $this->session->get('order');
            $this->session->set('sold_out',1);
            $this->session->getFlashBag()->add('sold_out', 'Désolé le musée est complet, vous ne pouvez commander plus de billets !');
        }
        return $order_in_progress;
    }

    /**
     * this method is used to display some feedback to user while his shopping
     * this method will also provide some useful information for check out
     * @param $order
     * @return array
     */
    public function buildOrder($order)
    {
        $number_of_tickets = count($order);
        $total = 0;
        foreach ($order as $key)
        {
            foreach ($key as $ticket)
            {
                $total += $ticket->getPrice();
            }
        }
        $order = [$total, $number_of_tickets];
        $this->session->set('total', $order[0]);
        $this->session->set('tickets', $order[1]);

        return $order;
    }

    /**
     * @param $param
     * @param $session_name
     * @return mixed
     */
    public function deleteTicketFromOrderInProgress($param, $session_name)
    {
        $this->policy->isOrderHasBegun('order');
        $id = $this->requestStack->getCurrentRequest()->attributes->get($param);
        $order = $this->session->get($session_name);
        unset($order[$id]);
        $this->buildOrder($order);
        return $this->session->set($session_name, $order);
    }

    /**
     * @param $param
     * @param $session_name
     * @return mixed
     */
    public function getTicketToModify($param, $session_name)
    {
        $id = $this->requestStack->getCurrentRequest()->attributes->get($param);
        $order = $this->session->get($session_name);
        $ticket_to_modify = $order[$id];
        return $ticket_to_modify;
    }

    /**
     * @param $param
     * @param $session_name
     * @param $name
     * @param $surname
     * @param $dob
     * @param $discount
     * @param $time_access
     * @return mixed
     */
    public function modifyTicket($param, $session_name, $name, $surname, $dob, $discount, $time_access)
    {
        //fetch date and order_token into session
        $date = $this->session->get('user_date');
        $order_token = $this->session->get('temp_order_token');
        //initialise requested classes
        $ticket = new Ticket();
        //->price type : age + discount + museum time access => ticket price
        $age = $this->tools->getAge($dob);
        $price = $this->tools->getPriceRange($age,$discount);
        $ticket_price = $this->tools->getTicketPrice($time_access,$price);
        $ticket->setDate($date)->getDate();
        $ticket->setName($name)->getName();
        $ticket->setSurname($surname)->getSurname();
        $ticket->setDob($dob)->getDob();
        $ticket->setDiscount($discount)->getDiscount();
        $ticket->setToken($name, $surname)->getToken();
        $ticket->setTimeAccess($time_access)->getTimeAccess($display = true);
        $ticket->setPrice($ticket_price)->getPrice();
        $ticket->setPriceType($dob,$discount);
        $ticket->setTimeAccessType($time_access)->getTimeAccessType();
        $ticket->setOrderToken($order_token);

        $id = $this->requestStack->getCurrentRequest()->attributes->get($param);
        $order = $this->session->get($session_name);
        $order[$id][] = $ticket;
        array_shift($order[$id]);
        $this->buildOrder($order);
        $this->session->set($session_name, $order);

        return $this->session->set($session_name, $order);
    }

    /**
     * @param Request $request
     * @param $timezone
     * @param $time
     * @return array
     */
    public function fillTicketAndProcess(Request $request, $timezone, $time)
    {
        //check that order process has started
        $this->policy->isOrderHasBegun('temp_order_token');
        //initialise entity
        $ticket = new Ticket();
        //create form
        $ticket_form = $this->formFactory->create(TicketType::class, $ticket);
        //check ticket type availability
        $full_day_ticket = $this->policy->isFullDayTicketAvailable(
            $timezone,
            $time
        );
        //process form
        $ticket_form->handleRequest($request);
        if ( $ticket_form->isSubmitted() && $ticket_form->isValid())
        {
          //fetch submitted data
          $name = $ticket_form->get('name')->getData();
          $surname = $ticket_form->get('surname')->getData();
          $dob = $ticket_form->get('dob')->getData();
          $discount = $ticket_form->get('discount')->getData();
          $time_access = $ticket_form->get('time_access')->getData();
          //create session order (cart)
          $this->addToOrder($this->createOrder(
              $name,
              $surname,
              $dob,
              $discount,
              $time_access
          ));
        }
        $this->policy->userLittleHelper();
        //prepare data to render in view
        return $render = [$ticket_form->createView(),$full_day_ticket];
    }

    /**
     * @param Request $request
     * @param $timezone
     * @param $time
     * @param $param
     * @param $session_name
     * @return array|void
     */
    public function modifyTicketAndProcess(Request $request, $timezone, $time, $param, $session_name)
    {
        //check that order process has started
        $this->policy->isOrderHasBegun('order');
        //initialise entity
        $ticket = new Ticket();
        //create form
        $ticket_form = $this->formFactory->create(TicketType::class, $ticket);
        //get ticket to modify for display
        $ticket = $this->getTicketToModify(
            $param,
            $session_name
        );
        foreach ($ticket as $key)
        {
            $display_dob = $key->getDob()->format('d-m-Y');
        }
        //check ticket type availability
        $full_day_ticket = $this->policy->isFullDayTicketAvailable(
            $timezone,
            $time
        );
        //process form
        $ticket_form->handleRequest($request);
        if ($ticket_form->isSubmitted() && $ticket_form->isValid())
        {
          //fetch submitted data
          $name = $ticket_form->get('name')->getData();
          $surname = $ticket_form->get('surname')->getData();
          $dob = $ticket_form->get('dob')->getData();
          $discount = $ticket_form->get('discount')->getData();
          $time_access = $ticket_form->get('time_access')->getData();
          //update ticket with modified data
          $this->modifyTicket($param, $session_name, $name, $surname, $dob, $discount, $time_access);
          //return a session var to look for into controller
          return $this->session->set('submitted', 1);
        }
        //prepare data to render in view
        $render = [$ticket,
            $ticket_form->createView(),$full_day_ticket,$display_dob];
        return $render;
    }

}