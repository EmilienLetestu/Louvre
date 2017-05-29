<?php
namespace EL\BookingBundle\Controller;
use EL\BookingBundle\Entity\Billing;
use EL\BookingBundle\Entity\Ticket;
use EL\BookingBundle\Form\OrderType;
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
       $order_form = $this->get('form.factory')->create(OrderType::class,$billing);


       if($order_form->handleRequest($request)->isSubmitted())
       {
           //fetch submitted data
           $billing = $ticket->setBilling($order_form->getData());

           //create session order (cart)
           $ticket_manager->createSession($billing);

           return $this->render('ELBookingBundle:Booking:booking.html.twig', array('order_form' => $order_form ->createView(),));
       }

       return $this->render('ELBookingBundle:Booking:booking.html.twig', array('order_form' => $order_form ->createView(),));
   }

}