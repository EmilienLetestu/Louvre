<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 13/06/2017
 * Time: 11:21
 */

namespace EL\BookingBundle\Validators;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class isCardExpYearOkValidator extends ConstraintValidator
{
  public function validate($value, Constraint $constraint)
  {
      $year = ($value);
      $now  =  date('y');
      $session = new Session();
      $session->set('y',$now);
      $diff = $year - $now;
      if($diff < 0)
      {
          $this->context->buildViolation($constraint->message)
                        ->addViolation();
      }
  }
}