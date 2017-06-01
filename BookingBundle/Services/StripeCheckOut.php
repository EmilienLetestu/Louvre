<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 22/05/2017
 * Time: 10:58
 */

namespace EL\BookingBundle\Services;

use Doctrine\ORM\EntityManager;
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
     * @return \Stripe\Charge
     */
    public function stripePayment($currency,$source)
    {
        $total  = $this->session->get('total');
        $api_key = $this->getApiKey();
        \Stripe\Stripe::setApiKey($api_key);
        $charge = \Stripe\Charge::create(array('amount'   => $total * 100,
                                               'currency' => $currency,
                                               'source'   => $source
        ));

        return $charge;
    }

}