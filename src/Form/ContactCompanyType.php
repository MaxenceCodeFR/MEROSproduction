<?php

namespace App\Form;

use App\Entity\ContactCompany;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ContactCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Mail',
                'required' => true,
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez fournir une adresse e-mail valide.'
                    ]),
                    new NotBlank([
                        'message' => 'L\'adresse e-mail est requise.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'john.doe@mail.com'
                ]
            ])
            ->add('company', TextType::class, [
                'label' => 'Entreprise',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom de l\'entreprise est requis.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de l\'entreprise'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le prénom est requis.'
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom est requis.'
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Début du contrat',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de début du contrat ne peut pas être antérieure à aujourd\'hui.'
                    ])
                ]
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fin du contrat',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de fin du contrat ne peut pas être antérieure à aujourd\'hui.'
                    ])
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Votre message',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 10000,
                        'maxMessage' => 'Le message ne peut pas dépasser {{ limit }} caractères.'
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Dites-nous en plus sur votre demande...'
                ]
            ])
            ->add('motif', EntityType::class, [
                'class' => 'App\Entity\Motif',
                'choice_label' => 'motif_company',
                'required' => true,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('condition', CheckboxType::class, [
                'label' => 'Réglement général sur la protection des données (RGPD)',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter le réglement général sur la protection des données.'
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactCompany::class,
        ]);
    }
}
