<?php

namespace App\Form;

use App\Entity\ContactCompany;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse Mail',
                'required' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('company', options: [
                'label' => 'Nom de l\'entreprise',

            ])
            ->add('firstname', options: [
                'label' => 'Prénom',

            ])
            ->add('lastname', options: [
                'label' => 'Nom',

            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Début du contrat',
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Fin du contrat',
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Votre message',
                'required' => false,
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
                'label' => 'J\'accepte que mes données soient utilisées pour me recontacter',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez cocher la case pour accepter les conditions d\'utilisation.',
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
