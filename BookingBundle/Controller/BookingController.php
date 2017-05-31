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
       //initialize needed services or classes
       $ticket_manager = $this->container->get('el_booking.ticketManager');
       $museum_policy = $this->container->get('el_booking.museumPolicy');
       $ticket =new Ticket();
       $ticket_form = $this->get('form.factory')->create(TicketType::class,$ticket);
       $full_day_ticket = $museum_policy->isFullDayTicketAvailable($timezone='Europe/Paris',$time=14);
       if($full_day_ticket == false){$ticket_form->remove('time_access');}

       if($ticket_form->handleRequest($request)->isSubmitted())
       {
           //fetch submitted data
           $name = $ticket_form->get('name')->getData();
           $surname = $ticket_form->get('surname')->getData();
           $dob = $ticket_form->get('dob')->getData();
           if($full_day_ticket == true)
           {
               $time_access = $ticket_form->get('time_access')->getData();
           }
           else
           {
               $time_access = 'p.m.';
           }
           $discount = $ticket_form->get('discount')->getData();
           //create session order (cart)
           $ticket_manager->addToOrder($ticket_manager->createOrder($name, $surname, $dob, $discount, $time_access));
           return $this->render('ELBookingBundle:Booking:booking.html.twig', array('ticket_form'=> $ticket_form->createView(),
                                                                                   'full_day_ticket'=>$full_day_ticket));
       }

       return $this->render('ELBookingBundle:Booking:booking.html.twig', array('ticket_form' => $ticket_form->createView(),
                                                                               'full_day_ticket'=>$full_day_ticket));
   }

}