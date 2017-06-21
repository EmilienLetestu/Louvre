<?php
namespace EL\BookingBundle\Controller;
use EL\BookingBundle\Entity\Billing;
use EL\BookingBundle\Form\StripeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 14:53
 */
class ChargeController extends Controller
{
    public function chargeAction(Request $request)
    {
        //initialize needed services
        $billing_manager = $this->container->get('el_booking.billingManager');
        //create form and process it
        $stripe_form = $billing_manager->stripeAndProcess($request,$currency ='eur');
        return $this->render('ELBookingBundle:Charge:charge.html.twig',['stripe_form' => $stripe_form[0]]);
    }
}