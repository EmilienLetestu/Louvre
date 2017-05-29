<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 17/05/2017
 * Time: 10:14
 */

namespace EL\BookingBundle\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->setMethod('POST')
            ->add('name', TextType::class, array('label' => 'Prénom'))
            ->add('surname', TextType::class, array('label' => 'Nom'))
            ->add('dob', DateType::class, array('label'  => 'Date de naissance',
                                                'widget' => 'text',
                                                'html5'  => false,
                                                'format' => 'dd-MM-yyyy'
            ))
            ->add('time_access', ChoiceType::class, array('label'   => 'Type de ticket',
                                                          'choices' => array('journée complète' =>'a.m.',
                                                                             '1/2 journée'      =>'p.m.'
                                                          )
            ))
            ->add('discount', CheckboxType::class, array('mapped'   => false,
                                                         'label'    => 'Je bénéficie d\'un tarif préférenciel',
                                                         'required' => false
            ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
       $resolver->setDefaults(array('data_class' => 'EL\BookingBundle\Entity\Ticket'));
    }
}