<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre du livre']
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'attr' => ['placeholder' => 'Nom de l\'auteur']
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('isbn', TextType::class, [
                'label' => 'ISBN',
                'required' => false,
                'attr' => [
                    'placeholder' => '9780123456789',
                    'maxlength' => 20
                ],
                'help' => 'ISBN-13 (13 chiffres commençant par 978 ou 979, avec ou sans tirets)'
            ])
            ->add('publicationYear', IntegerType::class, [
                'label' => 'Année de publication',
                'attr' => [
                    'min' => -3000,
                    'max' => 2024,
                    'placeholder' => 'Année de publication'
                ]
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Résumé',
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Résumé du livre'
                ]
            ])
            ->add('condition', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Excellent état' => Book::CONDITION_EXCELLENT,
                    'Bon état' => Book::CONDITION_GOOD,
                    'État moyen' => Book::CONDITION_FAIR,
                    'Mauvais état' => Book::CONDITION_POOR,
                ]
            ])
            ->add('stock', IntegerType::class, [
                'label' => 'Stock',
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Nombre d\'exemplaires (minimum 1)'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
