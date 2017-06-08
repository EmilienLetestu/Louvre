<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 08/06/2017
 * Time: 10:27
 */

namespace EL\BookingBundle\Validators;

use Symfony\Component\Validator\Constraint;

class isAgeOk extends Constraint
{
    /**
     * @var string
     */
    public $message = "L'âge doit être compris entre 0 et 100 ans";

    /**
     * @return string
     */
    public function validatedBy()
    {
       return get_class($this).'Validator';
    }

}