<?php

namespace App\Form;

use App\Entity\Social;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SocialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('instagram', TextType::class, [
                'label' => 'Instagram',
                'attr' => [
                    'placeholder' => 'https://www.instagram.com/...'
                ],
                'required' => false,
            ])
            ->add('snapchat', TextType::class, [
                'label' => 'Snapchat',
                'attr' => [
                    'placeholder' => 'Pseudo Snapchat'
                ],
                'required' => false,
            ])
            ->add('tiktok', TextType::class, [
                'label' => 'Tiktok',
                'attr' => [
                    'placeholder' => 'https://www.tiktok.com/...'
                ],
                'required' => false,
            ])
            ->add('twitter', TextType::class, [
                'label' => 'Twitter',
                'attr' => [
                    'placeholder' => 'https://www.twitter.com/...'
                ],
                'required' => false,
            ])
            ->add('facebook', TextType::class, [
                'label' => 'Facebook',
                'attr' => [
                    'placeholder' => 'https://www.facebook.com/...'
                ],
                'required' => false,
            ])
            ->add('pinterest', TextType::class, [
                'label' => 'Pinterest',
                'attr' => [
                    'placeholder' => 'https://www.pinterest.com/...'
                ],
                'required' => false,
            ])
            ->add('website', TextType::class, [
                'label' => 'Site web',
                'attr' => [
                    'placeholder' => 'https://www.monsite.com/...'
                ],
                'required' => false,
            ])
            ->add('youtube', TextType::class, [
                'label' => 'Youtube',
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/...'
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Social::class,
        ]);
    }
}
