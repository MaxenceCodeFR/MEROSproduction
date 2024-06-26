<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Entity\User;
use App\Entity\ContactCompany;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AffiliateInfluencerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            ])
            ->add('calendar', CalendarType::class, [
                'label' => 'Détails du calendrier',
                'required' => false,
                'include_color_options' => false, // Ne pas inclure les champs de couleur
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactCompany::class,
            'data_class' => Calendar::class,
            'include_influencer' => true,
        ]);
    }
}
