<?php

namespace App\Form;

use App\Entity\BookComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', HiddenType::class, [
                'attr' => ['class' => 'rating-value']
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire',
                'required' => true,
                'attr' => [
                    'rows' => 5,
                    'class' => 'form-control',
                    'placeholder' => 'Partagez votre avis sur ce livre...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BookComment::class,
        ]);
    }
}
