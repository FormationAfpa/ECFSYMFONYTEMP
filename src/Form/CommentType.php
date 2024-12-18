<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '5 étoiles' => 5,
                    '4 étoiles' => 4,
                    '3 étoiles' => 3,
                    '2 étoiles' => 2,
                    '1 étoile' => 1,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
                'label_attr' => ['class' => 'rating-label'],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Partagez votre avis sur ce livre...'
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
