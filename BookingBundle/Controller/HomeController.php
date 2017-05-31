<?php
namespace EL\BookingBundle\Controller;

use EL\BookingBundle\Form\CheckStatusType;

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
        $museum_policy = $this->container->get('el_booking.museumPolicy');
        $temp_order_manager = $this->container->get('el_booking.tempOrderManager');
        //end session if user as already bought his tickets
        $temp_order_manager->killSession($session_name="mail_sent");
        //check for disclaimer
        $disclaimer = $tools->getDisclaimer($timezone = 'Europe/Paris',$pm_access = 14);
        //create form
        $booking_status_form = $this->get('form.factory')->create(CheckStatusType::class);
        //processing form
        if($booking_status_form->handleRequest($request)->isSubmitted())
        {
          //fetch form data
          $date      = $booking_status_form->get('temp_order_date')->getData();
          $tickets = $booking_status_form->get('temp_number_of_tickets')->getData();
          //check total booking for requested date
          $total_booked = $museum_policy->getTotalBooked($date,$tickets);
          //use "total_booked" result => set some session var and redirect to home
          $redirect = $temp_order_manager->checkAvailabilityAndRedirect($total_booked,$booking_limit=1000,$date,$tickets,$prefix = 'Commande n°: ');
          return $this->redirectToRoute($redirect);
        }
        return $this->render('ELBookingBundle:Home:index.html.twig', array('booking_status_form'=> $booking_status_form->createView(),
                                                                           'disclaimer'         => $disclaimer
        ));
    }

}