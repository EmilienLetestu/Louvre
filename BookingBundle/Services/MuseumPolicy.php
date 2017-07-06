<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 16:49
 */

namespace EL\BookingBundle\Services;


use Doctrine\ORM\EntityManager;
use EL\BookingBundle\Entity\TempOrder;
use EL\BookingBundle\Managers\Tools;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        //check db
        $repository = $this->doctrine->getRepository('ELBookingBundle:Ticket');
        $check_booking = $repository->findBy(array('date' => $date));
        $count_booked = count($check_booking);

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
        if ($visit_day == $now->format('d-m-Y') && $time >= $pm_access)
        {
            $available = 0;
        }
        else
        {
            $available = 1;
        }
        $this->session->set('full_day_ticket',$available);
        return $available;
    }

    /**
     * @return mixed
     */
    public function userLittleHelper()
    {
        $requested_ticket = $this->session->get('user_n_tickets');
        $total_ordered = $this->session->get('tickets');
        if ($total_ordered < $requested_ticket)
        {
            $diff = $requested_ticket - $total_ordered;
            $this->session->set('reminder',$diff);
        }
        else
        {
            $date_time = $this->tools->formatDate($this->session->get('user_date'));
            $temp_order = new TempOrder();
            //change date format for db request
            $temp_order->setTempOrderDate(\DateTime::createFromFormat('m-d-Y H:i:s', $date_time));
            $check_stock = $this->getTotalBooked($temp_order->getTempOrderDate(),$total_ordered);
            $this->session->set('tickets_sold',$check_stock);
            $diff = $total_ordered - $requested_ticket;
            $this->session->set('reminder',"+{$diff}");
        }
        return $this->session->get('tickets_sold');
    }

    public function isOrderHasBegun($get_session)
    {
        if(!$this->session->has($get_session))
        {
            throw new NotFoundHttpException('La commande est vide ou n\'a pas commencée !');
        }
    }
}