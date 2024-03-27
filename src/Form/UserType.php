<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Specialty;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles', null, [
            //     'attr' => ['class' => 'json-array-field'] // Indiquer que 'roles' est un champ JSON
            // ])
            ->add('firstname')
            ->add('lastname')
            ->add('text')
            ->add('social', CollectionType::class, [
                'entry_type' => SocialType::class,
                'entry_options' => [
                    'label' => false, // pour ne pas afficher le label "Socials"
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (Fichier WebP)',
                'mapped' => false, 
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 500,
                        'maxHeight' => 500,
                        'mimeTypes' => ['image/webp'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide au format WebP.',
                    ]),
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). La taille maximale autorisée est de {{ limit }} {{ suffix }}.',
                    ])
                ],
            ])
            ->add('specialty', EntityType::class, [
                'class' => Specialty::class,
                'choice_label' => 'name', // Utiliser le champ 'name' de Specialty
                'multiple' => true,
                'expanded' => true,
            ]);
        if ($options['isCEO']){
            $builder->add('isFamous', CheckboxType::class, [
                'label' => 'Cocher si l\'influenceur est célèbre',
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
        $resolver->setRequired('isCEO');
    }
}
