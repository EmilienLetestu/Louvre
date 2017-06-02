<?php
namespace EL\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 15:29
 */
class CheckStatusType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('temp_order_date', DateType::class, array('label'  => 'Date souhaitée *',
                                                            'widget' => 'single_text', 'html5' => false,
                                                            'format' => 'dd-MM-yyyy',
                                                            'attr'   => ['class' => 'js-datepicker']
            ))

            ->add('temp_number_of_tickets', NumberType::class, array('label'  => 'Nombre d\'entrées souhaitées'));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class'      => 'EL\BookingBundle\Entity\TempOrder',
                                     'csrf_protection' => false
        ));
    }

}