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
         //initialize needed services or classes
         $billing = new Billing();
         $stripe_check_out = $this->container->get('el_booking.stripeCheckOut');
         $save_order       = $this->container->get('el_booking.saveOrder');
         $send_mail        = $this->container->get('el_booking.mail');

         //generate form
         $stripe_form = $this->get('form.factory')->create(StripeFormType::class,$billing);

        if($stripe_form->handleRequest($request)->isSubmitted() && $stripe_form->isValid())
        {
            //process form => submit data to stripe
            $name    = $stripe_form->get('name')->getData();
            $surname = $stripe_form->get('surname')->getData();
            $source  = $stripe_form->get('stripeToken')->getData();
            $email   = $stripe_form->get('email')->getData();

            //processing payment
            $stripe_check_out->stripePayment($currency='eur',$source);
            //save billing, save ticket,send mail
            $save_order->saveOrder($email,$name,$surname,$source);
            $send_mail ->sendMail($email);
            return $this->render('ELBookingBundle:Charge:charge.html.twig', array('stripe_form' => $stripe_form->createview()));
        }
        return $this->render('ELBookingBundle:Charge:charge.html.twig', array('stripe_form' => $stripe_form->createview()));
    }
}