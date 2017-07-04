<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 19/05/2017
 * Time: 09:02
 */

namespace EL\BookingBundle\Form;

use EL\BookingBundle\Validators\isCardExpYearOk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
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
                                                           'label' => 'Prénom du titulaire de la carte'
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
            ->add('email',EmailType::class,['constraints'=>[ new Email(['message' =>'Veuillez utiliser un e-mail valide !'])],
                                                             'label'  => 'Adresse email'
            ])
            ->add('number',TextType::class,['mapped' => false,
                                            'attr'   => ['placeholder' => 'numéro de la carte']
            ])
            ->add('cvc',TextType::class,['constraints' =>[ new NotBlank(),
                                                           new Type('numeric'),
                                                           new Length(['min' => 3,
                                                                       'max' => 3,
                                                                       'exactMessage' => ''])
                                                         ],
                                                'mapped' => false,
                                                'attr'   => ['placeholder' => '000']
            ])
            ->add('exp_month',NumberType::class,['constraints'=>[ new Type('numeric'),
                                                                  new Range(['min' => 1,
                                                                             'max' => 12,
                                                                             'minMessage' => '',
                                                                             'maxMessage' => ''
                                                                           ])
                                                                  ],
                                                                  'mapped' => false,
                                                                  'attr'   => ['placeholder' => 'mm'],
            ])
            ->add('exp_year',TextType::class,['constraints'=>[ new Type('numeric'),
                                                               new isCardExpYearOk()
                                                             ],
                                                             'mapped' => false,
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

