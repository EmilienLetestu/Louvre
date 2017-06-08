<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 08/06/2017
 * Time: 09:58
 */

namespace EL\BookingBundle\Validators;

use Symfony\Component\Validator\Constraint;

class isDayADayOff extends Constraint
{
    /**
     * @var string
     */
  public $message = "Le musée est fermé tous les mardis, veuillez choisir une autre date !";

  public function validatedBy()
  {
     return get_class($this).'Validator';
  }
}