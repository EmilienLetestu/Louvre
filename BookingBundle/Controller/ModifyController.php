<?php
namespace EL\BookingBundle\Controller;

use EL\BookingBundle\Entity\Ticket;
use EL\BookingBundle\Form\TicketType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


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
        //initialize needed services or classes
        $ticket = new Ticket();
        $ticket_manager = $this->container->get('el_booking.ticketManager');
        $museum_policy  = $this->container->get('el_booking.museumPolicy');
        //create form
        $ticket_form    = $this->get('form.factory')->create(TicketType::class,$ticket);
        //get ticket to modify to display it
        $ticket = $ticket_manager->getTicketToModify($param='id',$session_name='order');
        //check ticket type availability
        $full_day_ticket = $museum_policy->isFullDayTicketAvailable($timezone='Europe/Paris',$time=14);
        if($full_day_ticket == false){$ticket_form->remove('time_access');}
        if($ticket_form->handleRequest($request)->isSubmitted() && $ticket_form->isValid())
        {
            //fetch submitted data
            $name = $ticket_form->get('name')->getData();
            $surname = $ticket_form->get('surname')->getData();
            $dob = $ticket_form->get('dob')->getData();
            $discount = $ticket_form->get('discount')->getData();
            if($full_day_ticket == true)
            {
                $time_access = $ticket_form->get('time_access')->getData();
            }
            else
            {
                $time_access = 'p.m.';
            }
            //update ticket with modified data
            $ticket_manager->modifyTicket($param='id',$session_name='order',$name,$surname,$dob,$discount,$time_access);
            return $this->redirectToRoute('reservation_billetterie');
        }
        return $this->render('ELBookingBundle:Modify:modify.html.twig',array('modify'          => $ticket,
                                                                             'ticket_form'     =>$ticket_form->createView(),
                                                                             'full_day_ticket' => $full_day_ticket ));
    }
}