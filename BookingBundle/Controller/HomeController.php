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
        $session = new Session();
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


          if($total_booked < 1000)
          {
             $temp_order_manager->createOrderSession($date,$tickets,$prefix = 'Commande n°: ');
             return $this->redirectToRoute('accueil_billetterie');
          }
          else
          {
              $session->set('sold_out',1);
              return $this->redirectToRoute('accueil_billetterie');
          }
        }
        return $this->render('ELBookingBundle:Home:index.html.twig', array('booking_status_form'=> $booking_status_form->createView(),
                                                                           'disclaimer'         => $disclaimer
        ));
    }

}