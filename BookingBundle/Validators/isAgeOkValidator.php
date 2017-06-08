<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 08/06/2017
 * Time: 10:49
 */

namespace EL\BookingBundle\Validators;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class isAgeOkValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $year = date('Y', $value->getTimeStamp());
        $now  = date('Y');
        $age  = $now - $year;
        if($age < 0 || $age > 100)
        {
            $this->context->buildViolation($constraint->message)
                          ->addViolation();
        }
    }
}