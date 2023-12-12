<?php

namespace App\Form;

use App\Entity\ContactInfluencer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Validator\Constraints\File;

class ContactInfluencerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('motif', EntityType::class, [
                'class' => 'App\Entity\Motif',
                'choice_label' => 'motif_influencer',
                'required' => true,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])

            ->add('answer', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Votre message',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('cv', FileType::class, [
                'label' => 'CV (PDF file)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Mauvais type de fichier, veuillez choisir un fichier PDF valide',
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactInfluencer::class,
        ]);
    }
}
