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
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
   public function bookingAction(Request $request)
   {
       $ticket_manager = $this->get('el_booking.ticketManager')->fillTicketAndProcess(
           $request,
           $timezone = 'Europe/Paris',
           $time=14
       );

       return $this->render('ELBookingBundle:Booking:booking.html.twig', [
           'ticket_form'     => $ticket_manager[0],
           'full_day_ticket' => $ticket_manager[1]
       ]);
   }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction()
    {
        $this->get('el_booking.ticketManager')->deleteTicketFromOrderInProgress(
            $param='id',
            $session_name='order'
        );

        return $this->redirectToRoute('reservation_billetterie');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     */
    public function modifyAction(Request $request)
    {
        $session = $this->get('session');
        $ticket_manager = $this->get('el_booking.ticketManager')->modifyTicketAndProcess(
            $request,
            $timeZone     = 'Europe/Paris',
            $time         = 14,
            $param        = 'id',
            $session_name = 'order'
        );
        if($session->has('submitted'))
        {
            $session->remove('submitted');
            return $this->redirectToRoute('reservation_billetterie');
        }

        return $this->render('ELBookingBundle:Modify:modify.html.twig',[
            'modify'          => $ticket_manager[0],
            'ticket_form'     => $ticket_manager[1],
            'full_day_ticket' => $ticket_manager[2],
            'display_dob'     => $ticket_manager[3]
        ]);
    }
}