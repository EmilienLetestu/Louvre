<?php
namespace EL\BookingBundle\Controller;
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
        //initialize needed services or classes
        $session = new Session();
        $ticket_manager = $this->container->get('el_booking.ticketManager');
        $ticket_form = $ticket_manager->modifyTicketAndProcess($request,$timeZone='Europe/Paris',$time=14,$param='id',$session_name='order');
        if($session->has('submitted'))
        {
            $session->remove('submitted');
            return $this->redirectToRoute('reservation_billetterie');
        }
        return $this->render('ELBookingBundle:Modify:modify.html.twig',array('modify'          => $ticket_form['modify'],
                                                                             'ticket_form'     => $ticket_form['ticket_form'],
                                                                             'full_day_ticket' => $ticket_form['full_day_ticket']
        ));
    }
}