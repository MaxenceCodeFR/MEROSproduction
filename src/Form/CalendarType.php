<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le titre est obligatoire.'
                    ])
                ]
            ])
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de début ne peut pas être antérieure à aujourd\'hui.'
                    ])
                ]
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de fin ne peut pas être antérieure à aujourd\'hui.'
                    ])
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false
            ])
            ->add('all_day', CheckboxType::class, [
                'required' => false
            ]);

            if($options['include_influencer']){
                $builder
                ->add('user', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => function (User $user) {
                        return $user->getFirstname() . ' ' . $user->getLastname();
                    },
                    'required' => true,
                    'attr' => [
                        'class' => 'form-select'
                    ],
                    'query_builder' => function ($er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.roles LIKE :role')
                            ->setParameter('role', '%"ROLE_INFLUENCER"%');
                    },
                ]);
            }


        if ($options['include_color_options']) {
            $builder
                ->add('background_color', ColorType::class)
                ->add('border_color', ColorType::class)
                ->add('text_color', ColorType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'include_color_options' => true,
            'include_influencer' => true,
        ]);
    }
}
