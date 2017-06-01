<?php

namespace EL\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 14:53
 */
class DeleteController extends Controller
{

    public function deleteAction()
    {
        //initialize needed services or classes
        $ticket_manager = $this->container->get('el_booking.ticketManager');
        //get ticket to delete and delete it
        $ticket_manager->deleteTicketFromOrderInProgress($param='id',$session_name='order');
        return $this->redirectToRoute('reservation_billetterie');
    }

}