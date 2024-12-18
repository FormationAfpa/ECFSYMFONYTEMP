<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la salle',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom pour la salle',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Ex: Salle de réunion A',
                ],
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'Capacité',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez spécifier la capacité de la salle',
                    ]),
                    new Positive([
                        'message' => 'La capacité doit être un nombre positif',
                    ]),
                ],
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Nombre de personnes',
                ],
            ])
            ->add('hasWifi', CheckboxType::class, [
                'label' => 'Wi-Fi',
                'required' => false,
            ])
            ->add('hasProjector', CheckboxType::class, [
                'label' => 'Projecteur',
                'required' => false,
            ])
            ->add('hasWhiteboard', CheckboxType::class, [
                'label' => 'Tableau blanc',
                'required' => false,
            ])
            ->add('hasPowerOutlets', CheckboxType::class, [
                'label' => 'Prises électriques',
                'required' => false,
            ])
            ->add('hasTV', CheckboxType::class, [
                'label' => 'Télévision',
                'required' => false,
            ])
            ->add('hasAirConditioning', CheckboxType::class, [
                'label' => 'Climatisation',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
