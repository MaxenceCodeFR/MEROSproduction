<?php

namespace App\Form;

use App\Entity\Blog;
use Symfony\Component\Form\AbstractType;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Image;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre de l\'article ne peut pas être vide.'
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le titre ne peut pas dépasser 255 caractères.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le titre de l\'article'
                ]
            ])
            ->add('content', TinymceType::class, [
                'attr' => [
                    'toolbar' => 'bold italic underline | bullist numlist',
                    'class' => 'tinymce',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le contenu de l\'article ne peut pas être vide.'
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le contenu de l\'article doit contenir au moins 10 caractères.'
                    ])
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'article',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'L\'image ne peut pas être plus grande que 2MB.',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/webp',
                            'image/jpeg',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (jpeg, png, gif, webp).'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control-file'
                ]
            ])
            ->add('isArchived', CheckboxType::class, [
                'label' => 'Archiver l\'article ?',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
