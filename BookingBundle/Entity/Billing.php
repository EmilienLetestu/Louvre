<?php

namespace EL\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Billing
 *
 * @ORM\Table(name="billing")
 * @ORM\Entity(repositoryClass="EL\BookingBundle\Repository\BillingRepository")
 */
class Billing
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

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
     * @var int
     * @ORM\Column(name="number_of_tickets", type="smallint")
     */
    private $numberOfTickets;

    /**
     * @var \DateTime
     * @ORM\Column(name="visit_day", type="date")
     */
    private $visitDay;

    /**
     * @var string
     * @ORM\Column(name="token", type="string", length=255, unique=false)
     */
    private $token;

    /**
     * @var string
     * @ORM\Column(name="stripe_token", type="string", length=255, unique=true)
     */
    private $stripeToken;

    /**
     * @var int
     * @ORM\Column(name="price", type="integer")
     */
    private $price;


    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     * @param string $email
     * @return Billing
     */
    public function setEmail($email)
    {
        $this->email = strtolower($email);

        return $this;
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     * @param string $name
     * @return Billing
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
     * @return Billing
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
     * Set numberOfTickets
     * @param integer $numberOfTickets
     * @return Billing
     */
    public function setNumberOfTickets($numberOfTickets)
    {
        $this->numberOfTickets = $numberOfTickets;

        return $this;
    }

    /**
     * Get numberOfTickets
     * @return int
     */
    public function getNumberOfTickets()
    {
        return $this->numberOfTickets;
    }

    /**
     * Set visitDay
     * @param \DateTime $visitDay
     * @return Billing
     */
    public function setVisitDay($visitDay)
    {
        $this->visitDay = $visitDay;

        return $this;
    }

    /**
     * Get visitDay
     * @return \DateTime
     */
    public function getVisitDay()
    {
        return $this->visitDay;
    }

    /**
     * Set token
     * @param $token
     * @return Billing
     */
    public function setToken($token)
    {

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
     * Set stripeToken
     * @param string $stripeToken
     * @return Billing
     */
    public function setStripeToken($stripeToken)
    {
        $this->stripeToken = $stripeToken;

        return $this;
    }

    /**
     * Get stripeToken
     * @return string
     */
    public function getStripeToken()
    {
        return $this->stripeToken;
    }

    /**
     * Set price
     * @param integer $price
     * @return Billing
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

}

