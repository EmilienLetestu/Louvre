<?php
namespace EL\BookingBundle\Controller;
use EL\BookingBundle\Entity\Billing;
use EL\BookingBundle\Entity\Ticket;
use EL\BookingBundle\Form\TicketType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 14:52
 */
class BookingController extends Controller
{
   public function bookingAction(Request $request)
   {
       //initialize needed services or classes
       $ticket_manager = $this->get('el_booking.ticketManager');
       $ticket =new Ticket();
       $billing = new Billing();
       $ticket_form = $this->get('form.factory')->create(TicketType::class,$ticket);
       $session = new Session();
       var_dump($session->get('order'));

       if($ticket_form->handleRequest($request)->isSubmitted())
       {
           //fetch submitted data
           $name = $ticket_form->get('name')->getData();
           $surname = $ticket_form->get('surname')->getData();
           $dob = $ticket_form->get('dob')->getData();
           $time_access = $ticket_form->get('time_access')->getData();
           $discount = $ticket_form->get('discount')->getData();
           //create session order (cart)
           $ticket_manager->createSession($name, $surname, $dob, $discount, $time_access);
           return $this->render('ELBookingBundle:Booking:booking.html.twig', array('ticket_form'=> $ticket_form->createView()));
       }

       return $this->render('ELBookingBundle:Booking:booking.html.twig', array('ticket_form' => $ticket_form->createView()));
   }

}