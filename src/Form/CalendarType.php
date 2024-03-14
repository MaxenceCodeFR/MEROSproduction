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

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('start', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('end', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('description')
            ->add('all_day')
            ->add('user', EntityType::class, [
                'class' => 'App\Entity\User',
                //choice_label permet de choisir le champ qui sera affiché dans le select
                //Vu qu'il ne prend qu'une strinf, on concatène le firstname et le lastname
                'choice_label' => function (User $user) {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'required' => true,
                'attr' => [
                    'class' => 'form-select'
                ],
                //Pour affilier un influ il faut que je récupère seulement les influ:
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_INFLUENCER"%');
                },
            ]);

            if ($options['include_color_options']) {
                $builder->add('background_color', ColorType::class)
                    ->add('border_color', ColorType::class)
                    ->add('text_color', ColorType::class);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'include_color_options' => true, // Par défaut, incluez les options de couleur
        ]);
    }
}
