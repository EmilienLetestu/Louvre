<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 16:09
 */

namespace EL\BookingBundle\Managers;
class Tools
{
    /**
     * Display an information message
     * @param $time_zone
     * @param $pm_access
     * @return null|string
     */
    public function getDisclaimer($time_zone,$pm_access)
    {
       $time_to_check =$this->getTime($time_zone);

        if($time_to_check < $pm_access)
        {
            $disclaimer = null;
        }
        else
        {
            $disclaimer = "A partir de 14h tous billets achetés  pour une visite ce jour bénéficie du tarif 1/2 journée";
        }

        return $disclaimer;
    }

    /**
     * find current time
     * @param $time_zone
     * @return mixed
     */
    public function getTime($time_zone)
    {
        date_default_timezone_set($time_zone);
        $time = explode(':',date("H:i:s"));
        $time_to_check = $time[0];
        return $time_to_check;
    }

    /**
     * Find age from date of birth
     * @param $dob
     * @return int
     */
    public function getAge($dob)
    {
        $date = $dob;
        $now  = new \DateTime();
        $age  = $now->diff($date);

        return $age->y;
    }

    /**
     * calculate pricing
     * @param $dob
     * @return string
     */
    public function getTicketPriceType($dob)
    {
        $age = $this->getAge($dob);
        if($age < 4)                $type = 'Moins de 4ans';
        if($age >= 4 && $age < 12)  $type = 'Moins de 12ans';
        if($age >= 12 && $age < 60) $type = 'Adulte';
        if($age >= 60)              $type = 'Senior';
        return $type;
    }

    /**
     * Based on visitor age and special discount, this method will decide which price to apply
     * @param $age
     * @param $discount
     * @return int
     */
    public function getPriceRange($age,$discount)
    {
        if($age < 4) $price = 0;
        if($age >= 4 && $age < 12) $price = 8;
        if($age >= 12 && $age < 60) $price = 16;
        if($age >= 60) $price = 12;
        if($discount != null) $price = 10;
        return $price;
    }

    /**
     * display ticket type a user friendly way
     * @param $time_access
     * @return string
     */
    Public function displayTimeAccess($time_access)
    {
        if($time_access == "a.m.") $display_time = "journée complète";
        if($time_access == "p.m.") $display_time = "1/2 journée";

        return $display_time;
    }

    /**
     * Check if the price is full price or half-price
     * @param $time_access
     * @param $price
     * @return float|int
     */
    public function getTicketPrice($time_access,$price)
    {
        if($time_access == "p.m.") $ticket_price = $price/2;
        else($ticket_price = $price);

        return $ticket_price;
    }

    /**
     * collect all data from above method and build a user friendly ticket to display
     * @param $dob
     * @param $price
     * @param $time_access
     * @return array
     */
    public function getTicketType($dob, $price, $time_access)
    {
        $ticket_type = array();

        $ticket_type['price_type']   = $this->getTicketPriceType($dob);
        $ticket_type['price_range']  = $price;
        $ticket_type['time_access']  = $this->displayTimeAccess($time_access);
        $ticket_type['ticket_price'] = $this->getTicketPrice($time_access,$ticket_type['price_range']);

        return $ticket_type;
    }

    /**
     * turn the date variable stored into session to a date_time format friendly string
     * @param $date
     * @return string
     */
    public function formatDate($date)
    {
        //$date = $this->session->get('user_date');
        $time = ' 00:00:00';
        $date_time = $date.$time;
        return $date_time;
    }

}