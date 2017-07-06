<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 22/05/2017
 * Time: 10:58
 */

namespace EL\BookingBundle\Services;

use Doctrine\ORM\EntityManager;
use Stripe\Customer;
use Stripe\Error\Card;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Session\Session;

class StripeCheckOut
{
    private $doctrine;
    private $kernel;
    private $session;

    /**
     * StripeCheckOut constructor.
     * @param EntityManager $doctrine
     * @param Kernel $kernel
     * @param Session $session
     */
    public function __construct(
        EntityManager $doctrine,
        Kernel        $kernel,
        Session       $session
    )
    {
        $this->doctrine   = $doctrine;
        $this->kernel     = $kernel;
        $this->session    = $session;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        $env = $this->kernel->getEnvironment();
        return $env;
    }

    /**
     * will adjust api key on working environment
     * @return string
     */
    private function getApiKey()
    {
        $env = $this->kernel->getEnvironment();
        if($env == 'dev')
        {
            $api_key = 'sk_test_QLGdFvaHjglZtAQkI7m2N928';
        }
        else
        {
            $api_key = 'sk_live_keSdORUQjpgxbWmDmKTgIYAJ';
        }
        return $api_key;
    }

    /**
     * this method collect charging data, set api key and send them to stripe api
     * @param $currency
     * @param $source
     * @param $email
     * @param $name
     * @param $surname
     * @return mixed
     */
    public function stripePayment($currency,$source,$email,$name,$surname)
    {
        $total  = $this->session->get('total');
        $nbr_ticket = $this->session->get('tickets');
        $date = $this->session->get('user_date');
        $order_token = $this->session->get('temp_order_token');
        $api_key = $this->getApiKey();
        //create a message to display in case of network issue
        $message_to_user = 'Désolé mais nous rencontrons actuellement des problèmes, veuillez réessayer plus tard !';
        //create an array of error messages
        $card_error_message = ['card_declined'    => 'La transaction a échoué : carte bancaire refusé ! ',
                               'incorrect_cvc'    => 'La transaction a échoué : code de vérfication de la carte erroné !',
                               'expired_card'     => 'La transaction a échoué : carte bancaire expirée !',
                               'processing_error' => 'La transaction a échoué : une erreur de traitement est survenue, veuillez réesssayer !',
                               'incorrect_number' => 'La transaction a échoué : numéro de carte bancaire erroné !'
        ];
        try
        {
            \Stripe\Stripe::setApiKey($api_key);
           $customer =  \Stripe\Customer::create(["description" => "$order_token, $name $surname, billet(s) : $nbr_ticket, visite: $date ",
                                                  "email"       => $email,
                                                  "source"      => $source
           ]);
            \Stripe\Charge::create(['amount'   => $total * 100,
                                    'currency' => $currency,
                                    'customer' => $customer['id']

            ]);
            $this->session->set('payment_success',1);
            $this->session->set('customer',$customer['id']);
        }
        catch (\Stripe\Error\Card $e)
        {
            $body = $e->getJsonBody();
            $err  = $body['error'];
            $message = $card_error_message[$err['code']];
            $this->session->getFlashBag()->add('error',$message);
        }
        catch (\Stripe\Error\RateLimit $e)
        {
            $this->session->getFlashBag()->add('error',$message_to_user);
        }
        catch (\Stripe\Error\InvalidRequest $e)
        {
            $this->session->getFlashBag()->add('error',$message_to_user);
        }
        catch (\Stripe\Error\Authentication $e)
        {
            $this->session->getFlashBag()->add('error',$message_to_user);
        }
        catch (\Stripe\Error\ApiConnection $e)
        {
            $this->session->getFlashBag()->add('error',$message_to_user);
        }
        catch (\Stripe\Error\Base $e)
        {
            $this->session->getFlashBag()->add('error',$message_to_user);
        }
        if(!$this->session->has('payment_success') && isset($customer['id']))
        {
            $customer_delete = \Stripe\Customer::retrieve($customer['id']);
            $customer_delete->delete();
        }
        return $this->session->get('payment_success');
    }
}