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
        return $this->render('ELBookingBundle:Modify:modify.html.twig',['modify'          => $ticket_form[0],
                                                                        'ticket_form'     => $ticket_form[1],
                                                                        'full_day_ticket' => $ticket_form[2],
                                                                        'display_dob'     => $ticket_form[3]
        ]);
    }
}