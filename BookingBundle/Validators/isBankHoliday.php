<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 07/06/2017
 * Time: 16:09
 */
namespace EL\BookingBundle\Validators;

use Symfony\Component\Validator\Constraint;
/**
 * @Annotation
 */
class isBankHoliday extends Constraint
{
    public $message = "Le musée est fermé à cette date, veuillez choisr une autre date";

    public function validateBy()
    {
        return get_class($this).'Validator';
    }
}

