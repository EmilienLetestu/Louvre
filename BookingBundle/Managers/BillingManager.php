<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 05/06/2017
 * Time: 16:29
 */

namespace EL\BookingBundle\Managers;


use EL\BookingBundle\Entity\Billing;
use EL\BookingBundle\Form\StripeFormType;
use EL\BookingBundle\Services\Mail;
use EL\BookingBundle\Services\MuseumPolicy;
use EL\BookingBundle\Services\SaveOrder;
use EL\BookingBundle\Services\StripeCheckOut;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class BillingManager
{
    private $formFactory;
    private $checkOut;
    private $save;
    private $mail;
    private $session;
    private $tools;

    public function __construct(
        FormFactory    $formFactory,
        StripeCheckOut $checkOut,
        SaveOrder      $save,
        Mail           $mail,
        Session        $session,
        MuseumPolicy   $policy
    )
    {
        $this->formFactory = $formFactory;
        $this->checkOut    = $checkOut;
        $this->save        = $save;
        $this->mail        = $mail;
        $this->session     = $session;
        $this->policy      = $policy;
    }

    /**
     * @param Request $request
     * @param $currency
     * @return array
     */
    Public function stripeAndProcess(Request $request,$currency)
    {
        $this->policy->isOrderHasBegun($get_session = 'order');
        //initialise entity
        $billing = new Billing();
        //create form
        $stripe_form = $this->formFactory->create(StripeFormType::class,$billing);
        //process form
        $stripe_form->handleRequest($request);
        if($stripe_form->isSubmitted() && $stripe_form->isValid())
        {
            //get submitted data
            $name    = $stripe_form->get('name')->getData();
            $surname = $stripe_form->get('surname')->getData();
            $source  = $stripe_form->get('stripeToken')->getData();
            $email   = $stripe_form->get('email')->getData();
            //processing payment
            $this->checkOut->stripePayment($currency,$source,$email,$name,$surname);
            if($this->session->has('payment_success'))
            {
                //save billing, save ticket,send mail
                $this->save->saveOrder($email, $name, $surname, $source);
                $this->mail->sendMail($email);
                $this->session->getFlashBag()->add('success', 'Votre paiement a été effectué avec succès, consultez votre boîte mail pour obtenir vos billets');
            }
        }
        //prepare data to render in view
        $render = [$stripe_form->createview()];
        return $render;
    }
}