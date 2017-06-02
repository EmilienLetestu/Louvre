<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 16:49
 */

namespace EL\BookingBundle\Services;


use Doctrine\ORM\EntityManager;
use EL\BookingBundle\Managers\Tools;
use Symfony\Component\HttpFoundation\Session\Session;

class MuseumPolicy
{
    private $session;
    private $doctrine;
    private $tools;

    /**
     * MuseumPolicy constructor.
     * @param Session $session
     * @param EntityManager $doctrine
     * @param Tools $tools
     */
    public function __construct(
        Session       $session,
        EntityManager $doctrine,
        Tools         $tools
    )
    {
        $this->session  = $session;
        $this->doctrine = $doctrine;
        $this->tools    = $tools;
    }

    /**
     * get total ticket sales for a given date
     * @param $date
     * @param $tickets
     * @return int
     */
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

    /**
     * this method will check if user is buying ticket for today,
     * if so the method will check current time to see if user can order full day ticket or not
     * @param $timezone
     * @param $pm_access
     * @return bool
     */
    public function isFullDayTicketAvailable($timezone,$pm_access)
    {
        $now  = new \DateTime();
        $time = $this->tools->getTime($timezone);
        $visit_day = $this->session->get('user_date');
        if ($visit_day == $now->format('m-d-Y') && $time >= $pm_access)
        {
            $available = false;
        }
        else
        {
            $available = true;
        }
        $this->session->set('full_day_ticket',$available);
        return $available;
    }
}