<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 15:34
 */

namespace EL\BookingBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class TempOrder
{
    private $id;
    private $tempOrderDate;
    /**
     * @var
     * @Assert\NotBlank()
     * @Assert\Range(min=1,max=10,minMessage = "fddfdggdfg")
     * @var
     */
    private $tempNumberOfTickets;
    /**
     * @var
     */
    private $tempOrderToken;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $tempOrderDate
     */
    public function setTempOrderDate($tempOrderDate)
    {
        $this->tempOrderDate = $tempOrderDate;
    }

    /**
     * @return mixed
     */
    public function getTempOrderDate()
    {
        return $this->tempOrderDate;
    }

    /**
     * @param mixed $tempNumberOfTickets
     */
    public function setTempNumberOfTickets($tempNumberOfTickets)
    {
        $this->tempNumberOfTickets = $tempNumberOfTickets;
    }

    /**
     * @return mixed
     */
    public function getTempNumberOfTickets()
    {
        return $this->tempNumberOfTickets;
    }

    /**
     * @param $prefix
     */
    public function setTempOrderToken($prefix)
    {
        $tempOrderToken = $this->createTempOrderToken($prefix);
        $this->tempOrderToken = $tempOrderToken;
    }

    /**
     * @return mixed
     */
    public function getTempOrderToken()
    {
        return $this->tempOrderToken;
    }


    private function createTempOrderToken($prefix)
    {
        $temp_order_token = uniqid($prefix);
        return $temp_order_token;
    }

}