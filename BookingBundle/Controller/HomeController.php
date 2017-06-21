<?php
namespace EL\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 14:51
 */
class HomeController extends Controller
{
    public function indexAction(Request $request)
    {   //initialise needed service(s)
        $temp_order_manager = $this->container->get('el_booking.tempOrderManager');
        //create and process form
        $booking_status_form = $temp_order_manager->checkStatusAndProcess($request,$timezone = 'Europe/Paris',$pm_access = 14,$booking_limit = 1000,$prefix = 'Commande n°: ');
        return $this->render('ELBookingBundle:Home:index.html.twig', ['booking_status_form'=> $booking_status_form[0],
                                                                      'disclaimer'         => $booking_status_form[1]
        ]);
    }
}