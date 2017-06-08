<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 07/06/2017
 * Time: 16:09
 */
namespace EL\BookingBundle\Validators;

use Symfony\Component\Validator\Constraint;

class isBankHoliday extends Constraint
{
    /**
     * @var string
     */
    public $message = "Le musée est fermé à cette date, veuillez choisr une autre date";

    /**
     * @return string
     */
    public function validateBy()
    {
        return get_class($this).'Validator';
    }
}

