<?php
namespace EL\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
       return $this->render('ELBookingBundle:Booking:booking.html.twig', ['ticket_form'    => $ticket_form[0],
                                                                          'full_day_ticket'=> $ticket_form[1]
       ]);
   }

    public function deleteAction()
    {
        //initialize needed services or classes
        $ticket_manager = $this->container->get('el_booking.ticketManager');
        //get ticket to delete and delete it
        $ticket_manager->deleteTicketFromOrderInProgress($param='id',$session_name='order');
        return $this->redirectToRoute('reservation_billetterie');
    }


}