<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', options: ['label' => 'Titre de l\'article'])
            ->add('content', TinymceType::class, [
                "attr" => [
                    "toolbar" => "bold italic underline | bullist numlist",
                ],
            ])
            ->add('image', FileType::class, [
                "mapped" => false,
                "required" => false,
            ])
            ->add('isArchived', options: ['label' => 'Archiver l\'article ?']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
