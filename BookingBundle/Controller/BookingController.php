<?php
namespace EL\BookingBundle\Controller;
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
       //initialize needed services
       $ticket_manager = $this->container->get('el_booking.ticketManager');
       //create form and process it
       $ticket_form =  $ticket_manager->fillTicketAndProcess($request,$timezone='Europe/Paris',$time=14);
       return $this->render('ELBookingBundle:Booking:booking.html.twig', array('ticket_form'    => $ticket_form['ticket_form'],
                                                                               'full_day_ticket'=> $ticket_form['full_day_ticket']));
   }

}