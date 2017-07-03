<?php
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 17/05/2017
 * Time: 10:14
 */

namespace EL\BookingBundle\Form;



use EL\BookingBundle\Validators\isAgeOk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->setMethod('POST')
            ->add('name', TextType::class, ['constraints'=>[ new NotBlank(),
                                                             new Type('string'),
                                                             new Length(['min'        => 3,
                                                                         'max'        => 50,
                                                                         'minMessage' => 'Le prénom doit comporter 3 caractères minimum !',
                                                                         'maxMessage' => 'Le prénom est limité à 50 caractères !'])
                                                            ],
                                             'label' => 'Prénom'
            ])
            ->add('surname', TextType::class, ['constraints'=>[ new NotBlank(),
                                                                new Type('string'),
                                                                new Length(['min' => 3,
                                                                            'max' => 50,
                                                                            'minMessage' => 'Le nom doit comporter 3 caractères minimum !',
                                                                            'maxMessage' => 'Le nom est limité à 50 caractères !'])
                                                                ],
                                                'label' => 'Nom'
            ])
            ->add('dob', DateType::class,['constraints' =>[ new NotBlank(),
                                                            new DateTime(),
                                                            new isAgeOk()
                                                          ],
                                          'label'  => 'Date de naissance',
                                          'widget' => 'choice',
                                          'html5'  => false,
                                          'format' =>'dd-MM-yyyy',
                                          'years'  => range(date('Y') - 95, date('Y') + 0),
                                          'placeholder' =>['year'  => 'Année',
                                                           'month' => 'Mois',
                                                           'day' =>   'Jour'
                                          ]
            ])
            ->add('time_access', ChoiceType::class, ['label'    => 'Type de ticket',
                                                     'choices'  => ['journée complète' => 'a.m.',
                                                                    '1/2 journée'      => 'p.m.'
                                                     ]
            ])
            ->add('discount', CheckboxType::class, ['mapped'   => false,
                                                    'label'    => 'Je bénéficie d\'un tarif préférenciel',
                                                    'required' => false
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
       $resolver->setDefaults(['data_class'  => 'EL\BookingBundle\Entity\Ticket',
                               'time_access' => true
       ]);
    }
}