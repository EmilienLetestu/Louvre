<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 29/05/2017
 * Time: 13:43
 */

namespace EL\BookingBundle\Form;


use EL\BookingBundle\Entity\Billing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('tickets', CollectionType::class,['entry_type'=>TicketType::class,
                                                   'allow_add' =>true,
                                                   'allow_delete'=>true,

           ])
           ->add('save',SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'EL\BookingBundle\Entity\Billing'));
    }
}