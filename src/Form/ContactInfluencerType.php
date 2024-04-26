<?php

namespace App\Form;

use App\Entity\ContactInfluencer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Regex;

class ContactInfluencerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'required' => true,
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez fournir une adresse email valide.'
                    ]),
                    new NotBlank([
                        'message' => 'L\'adresse email est requise.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'john.doe@mail.com'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le numéro de téléphone est requis.'
                    ]),
                    new Regex([
                        'pattern' => '/^(\+33|0)[1-9](\d{2}){4}$/',
                        'message' => 'Le format du numéro de téléphone est invalide. Formats acceptés : 0123456789 ou +33123456789.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0123456789 ou +33123456789'
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
                'constraints' => [
                    new Length([
                        'max' => 2000,
                        'maxMessage' => 'La réponse ne peut pas excéder 2000 caractères.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'J\'ai connu Meros grâce à...'
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Votre message',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 3000,
                        'maxMessage' => 'Votre message ne peut pas dépasser 3000 caractères.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Dîtes nous en plus sur vous...'
                ]
            ])
            ->add('cv', FileType::class, [
                'label' => 'CV (fichier PDF) N\'envoyez pas votre CV si votre requête concerne une demande d\'information',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez choisir un fichier PDF valide.'
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('condition', CheckboxType::class, [
                'label' => 'J\'accepte que mes données soient utilisées pour me recontacter',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les conditions pour continuer.'
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactInfluencer::class,
        ]);
    }
}
