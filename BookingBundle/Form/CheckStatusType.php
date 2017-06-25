<?php
namespace EL\BookingBundle\Form;

use EL\BookingBundle\Validators\isBankHoliday;
use EL\BookingBundle\Validators\isDayADayOff;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

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
            ->add('temp_order_date', DateType::class, ['constraints'=>[ new isBankHoliday(),
                                                                        new isDayADayOff(),
                                                                      ],
                                                       'label'     => 'Date souhaitée',
                                                       'widget'    => 'single_text', 'html5' => false,
                                                       'format'    => 'dd-MM-yyyy',
                                                       'attr'      => ['class'    => 'js-datepicker',
                                                                       'readOnly' => 'true']
                                                      ]
            )
            ->add('temp_number_of_tickets', NumberType::class, ['constraints'=>[new Type('numeric',['message'=>'Ce champs n\'accepte que les chiffres']),
                                                                                new Range(['min' => 1,
                                                                                           'max' => 10,
                                                                                           'minMessage' =>'Le nombre minimum de billets est 1',
                                                                                           'maxMessage' =>'10 billets maximum par commande '])
                                                                               ],
                                                                'label' => 'Nombre d\'entrées souhaitées'
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'      => 'EL\BookingBundle\Entity\TempOrder',
                                'csrf_protection' => false
        ]);
    }

}