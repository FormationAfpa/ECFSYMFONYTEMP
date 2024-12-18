<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'abonnement',
                'choices' => $options['type_choices'],
                'expanded' => false,
                'multiple' => false,
                'disabled' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
            'type_choices' => [
                'Mensuel' => 'mensuel',
                'Annuel' => 'annuel',
            ],
        ]);
    }
}
