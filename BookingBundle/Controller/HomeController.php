<?php
namespace EL\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 14:51
 */
class HomeController extends Controller
{
    public function indexAction(Request $request)
    {
        //initialize needed services or classes
        $tools = $this->container->get('el_booking.tools');
        $session = new Session();
        $temp_order_manager = $this->container->get('el_booking.tempOrderManager');
        //end session if user as already bought his tickets
        $temp_order_manager->killSession($session_name="mail_sent");
        $disclaimer = $tools->getDisclaimer($timezone = 'Europe/Paris',$pm_access = 14);
        //create and process form
        $booking_status_form = $temp_order_manager->checkStatusAndProcess($request,$booking_limit = 1000,$prefix = 'Commande n°: ');
        if($session->has('submitted'))
        {
            $session->remove('submitted');
            return $this->redirectToRoute('accueil_billetterie');
        }
        return $this->render('ELBookingBundle:Home:index.html.twig', array('booking_status_form'=> $booking_status_form['booking_status_form'],
                                                                           'disclaimer'         => $disclaimer
        ));
    }

}