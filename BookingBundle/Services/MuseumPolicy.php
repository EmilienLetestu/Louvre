<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 16:49
 */

namespace EL\BookingBundle\Services;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class MuseumPolicy
{
    private $session;
    private $doctrine;

    /**
     * TempOrderManager constructor.
     * @param Session $session
     * @param EntityManager $doctrine
     */
    public function __construct(
        Session       $session,
        EntityManager $doctrine
    )
    {
        $this->session  = $session;
        $this->doctrine = $doctrine;
    }

    public function getTotalBooked($date,$tickets)
    {
        //To do check session
        //....
        //check db
        $repository = $this->doctrine->getRepository('ELBookingBundle:Ticket');
        $check_booking_on_user_date = $repository->findBy(array('date' => $date));
        $count_booked = count($check_booking_on_user_date);

        $total_booked = $count_booked + $tickets;

        return $total_booked;

    }
}