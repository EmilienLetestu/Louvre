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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;


class StripeFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->setMethod('POST')
            ->add('name',TextType::class,['constraints'=>[ new NotBlank(),
                                                           new Type('string'),
                                                           new Length(['min'        => 3,
                                                                       'max'        => 50,
                                                                       'minMessage' => 'Le prénom doit comporter 3 caractères minimum !',
                                                                       'maxMessage' => 'Le prénom est limité à 50 caractères !'])
                                                           ],
                                                           'label' => 'Prenom du titulaire de la carte'
            ])
            ->add('surname',TextType::class,['constraints'=>[ new NotBlank(),
                                                              new Type('string'),
                                                              new Length(['min'        => 3,
                                                                          'max'        => 50,
                                                                          'minMessage' => 'Le nom doit comporter 3 caractères minimum !',
                                                                          'maxMessage' => 'Le nom est limité à 50 caractères !'])
                                                              ],
                                                              'label' => 'Nom du titulaire de la carte'
            ])
            ->add('email',EmailType::class,['constraints'=>[new Email()],
                                                            'label'  => 'Adresse email'
            ])
            ->add('number',TextType::class,['mapped' => false,
                                            'attr'   => ['placeholder' => 'numéro de la carte']
            ])
            ->add('cvc',TextType::class,['mapped' => false,
                                         'attr'   => ['placeholder' => 'cvc']
            ])
            ->add('exp_month',TextType::class,['mapped' => false,
                                               'attr'   => ['placeholder' => 'mm']
            ])

            ->add('exp_year',TextType::class,['mapped' => false,
                                              'attr'   => ['placeholder' => 'aa']
            ])
            ->add('stripeToken',HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'EL\BookingBundle\Entity\Billing']);
    }
}

