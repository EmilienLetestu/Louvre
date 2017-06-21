<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 07/06/2017
 * Time: 16:16
 */

namespace EL\BookingBundle\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class isBankHolidayValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $bank_holiday = ['01/05','01/11','25/12'];
        $date = date('d/m', $value->getTimeStamp());
        foreach ($bank_holiday as $holiday)
        {
            if($date === $holiday)
            {
                $this->context->buildViolation($constraint->message)
                              ->addViolation();
            }
        }
    }
}