<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 19/05/2017
 * Time: 09:02
 */

namespace EL\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class StripeFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->setMethod('POST')
            ->add('name',TextType::class, array('label'    => 'Prenom du titulaire de la carte'))
            ->add('surname',TextType::class, array('label' => 'Nom du titulaire de la carte'))
            ->add('email',EmailType::class, array('label'  => 'Adresse email'))
            ->add('number',TextType::class, array('mapped' => false,
                                                   'label' => 'numéro de la carte de payement'
            ))
            ->add('cvc',TextType::class, array('mapped' => false,
                                                'label' => 'code'
            ))
            ->add('exp_month',TextType::class, array('mapped'  => false,
                                                      'label'  => 'expire le',
                                                      'attr'   => array('placeholder' => 'mm'
                )))

            ->add('exp_year',TextType::class, array('mapped'  => false,
                                                      'attr'  => array('placeholder' => 'aa'
                )))
            ->add('stripeToken',HiddenType::class)
            ->add('submit',SubmitType::class, array('attr' => array('label' => 'Valider et payer' )))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'EL\BookingBundle\Entity\Billing'));
    }
}

