<?php
namespace EL\BookingBundle\Controller;

use EL\BookingBundle\ELBookingBundle;
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
class ModifyController extends Controller
{
    public function modifyAction(Request $request)
    {
        $ticket = new Ticket();
        $session = new Session();
        $ticket_manager = $this->container->get('el_booking.ticketManager');
        $ticket_form    = $this->get('form.factory')->create(TicketType::class,$ticket);
        $ticket = $ticket_manager->getTicketToModify($query='ticket',$session_name='order');
        if($ticket_form->handleRequest($request)->isSubmitted() && $ticket_form->isValid())
        {
            //fetch submitted data
            $name = $ticket_form->get('name')->getData();
            $surname = $ticket_form->get('surname')->getData();
            $dob = $ticket_form->get('dob')->getData();
            $time_access = $ticket_form->get('time_access')->getData();
            $discount = $ticket_form->get('discount')->getData();

            $ticket_manager->modifyTicket($query='ticket',$session_name='order',$name,$surname,$dob,$discount,$time_access);

            return $this->redirectToRoute('reservation_billetterie');

        }
        return $this->render('ELBookingBundle:Modify:modify.html.twig',array('ticket'      => $ticket,
                                                                             'ticket_form' =>$ticket_form->createView()));
    }
}