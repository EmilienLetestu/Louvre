<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 08/06/2017
 * Time: 10:06
 */

namespace EL\BookingBundle\Validators;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class isDayADayOffValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value,Constraint $constraint)
    {
        $day_off = 'Tue';
        $day = date('D',$value->getTimeStamp());

       if($day === $day_off)
       {
           $this->context->buildViolation($constraint->message)
                         ->addViolation();
       }
    }
}