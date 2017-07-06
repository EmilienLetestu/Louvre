<?php

namespace EL\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EL\BookingBundle\Managers\Tools;

/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="EL\BookingBundle\Repository\TicketRepository")
 */
class Ticket
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(name="time_access", type="string", length=255)
     */
    private $timeAccess;

    /**
     * @var string
     * @ORM\Column(name="price", type="string", length=255)
     */
    private $price;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var \DateTime
     * @ORM\Column(name="dob", type="date")
     */
    private $dob;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string
     * @ORM\Column(name="discount", type="string", length=255)
     */
    private $discount = null;

    /**
     * @var string
     * @ORM\Column(name="order_token", type="string", length=255)
     */
    private $orderToken;

    /**
     * @var
     * only use on session
     * display a user friendly pricing
     */
    private $priceType;

    /**
     * @var
     * only use on session
     * display a user friendly ticket type
     */
    private $timeAccessType;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="Billing")
     * @ORM\JoinColumn(nullable=false)
     */
    private $billing;


    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     * @param \DateTime $date
     * @return Ticket
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set timeAccess
     * @param string $timeAccess
     * @return Ticket
     */
    public function setTimeAccess($timeAccess)
    {
        $this->timeAccess = $timeAccess;

        return $this;
    }

    /**
     * Get timeAccess
     * @return string
     */
    public function getTimeAccess()
    {
        return $this->timeAccess;
    }

    /**
     * Set price
     * @param string $price
     * @return Ticket
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set name
     * @param string $name
     * @return Ticket
     */
    public function setName($name)
    {
        $this->name = strip_tags(ucfirst(mb_strtolower($name)));

        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     * @param string $surname
     * @return Ticket
     */
    public function setSurname($surname)
    {
        $this->surname = strip_tags(ucfirst(mb_strtolower($surname)));

        return $this;
    }

    /**
     * Get surname
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set dob
     * @param \DateTime $dob
     * @return Ticket
     */
    public function setDob($dob)
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Get dob
     * @return \DateTime
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * Set token
     * @param $name
     * @param $surname
     * @return $this
     */
    public function setToken($name,$surname)
    {
        $token = $this->generateToken(
            ucfirst($name),
            ucfirst($surname)
        );
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set discount
     * @param $discount
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param $orderToken
     * @return $this
     */
    public function setOrderToken($orderToken)
    {
        $this->orderToken = $orderToken;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderToken()
    {
        return $this->orderToken;
    }

    /**
     * @param $dob
     * @param $discount
     * @return $this
     */
    public function setPriceType($dob,$discount)
    {
       $tools = new Tools();
       $type = $tools->getTicketPriceType($dob,$discount);
       $this->priceType = $type;
       return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * @param $timeAccess
     * @return $this
     */
    public function setTimeAccessType($timeAccess)
    {
       $tools = new Tools();
       $type = $tools->displayTimeAccess($timeAccess);
       $this->timeAccessType = $type;
       return $this;
    }

    /**
     * @return mixed
     */
    public function getTimeAccessType()
    {
        return $this->timeAccessType;
    }

    /**
     * @param Billing $billing
     * @return $this
     */
    public function setBilling(Billing $billing)
    {
        $this->billing = $billing;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBilling()
    {
        return $this->billing;
    }

    /**
     * @param $name
     * @param $surname
     * @return string
     */
    private function generateToken($name,$surname)
    {
        $prefix = "{$name[0]}_{$surname[0]}: ";
        $token = uniqid($prefix);
        return $token;
    }


}

