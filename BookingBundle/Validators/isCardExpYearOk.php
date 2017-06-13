<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 13/06/2017
 * Time: 11:07
 */

namespace EL\BookingBundle\Validators;

use Symfony\Component\Validator\Constraint;

class isCardExpYearOk extends Constraint
{
    /**
     * @var string
     */
    public $message = "";

    /**
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

}