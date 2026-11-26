<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Tutorial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutorialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Tutorial Title',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter the tutorial title',
                ],
            ])

            ->add('content', TextareaType::class, [
                'label' => 'Tutorial Content',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 8,
                    'placeholder' => 'Write your tutorial here...',
                ],
            ])

            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Category',
                'placeholder' => 'Choose a category',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'label' => 'Tags',
                'multiple' => true,
                'expanded' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tutorial::class,
        ]);
    }
}