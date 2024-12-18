<?php

namespace App\Form;

use App\Entity\RoomReservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class RoomReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startTime', DateTimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                    'max' => (new \DateTime('+1 month'))->format('Y-m-d\TH:i'),
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une heure de début']),
                    new GreaterThanOrEqual([
                        'value' => 'today 08:00',
                        'message' => 'Les réservations ne sont possibles qu\'à partir de 8h'
                    ]),
                    new LessThanOrEqual([
                        'value' => 'today 19:00',
                        'message' => 'Les réservations doivent commencer avant 19h'
                    ])
                ]
            ])
            ->add('endTime', DateTimeType::class, [
                'label' => 'Heure de fin',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i'),
                    'max' => (new \DateTime('+1 month'))->format('Y-m-d\TH:i'),
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir une heure de fin']),
                    new GreaterThan([
                        'propertyPath' => 'parent.all[startTime].data',
                        'message' => 'L\'heure de fin doit être après l\'heure de début'
                    ]),
                    new LessThanOrEqual([
                        'value' => 'today 19:00',
                        'message' => 'Les réservations doivent se terminer avant 19h'
                    ])
                ]
            ])
            ->add('purpose', TextType::class, [
                'label' => 'Objet de la réservation',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez indiquer l\'objet de votre réservation']),
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'L\'objet de la réservation doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'L\'objet de la réservation ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RoomReservation::class,
        ]);
    }
}
