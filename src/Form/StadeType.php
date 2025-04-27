<?php

namespace App\Form;

use App\Entity\Stade;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class StadeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du stade',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                    'placeholder' => 'Entrez le nom du stade'
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                    'placeholder' => 'Entrez la ville'
                ]
            ])
            ->add('capaciteVirage', NumberType::class, [
                'label' => 'Capacité virage',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                    'placeholder' => 'Entrez la capacité du virage'
                ]
            ])
            ->add('capacitePelouse', NumberType::class, [
                'label' => 'Capacité pelouse',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                    'placeholder' => 'Entrez la capacité de la pelouse'
                ]
            ])
            ->add('capaciteEnceinte', NumberType::class, [
                'label' => 'Capacité enceinte',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                    'placeholder' => 'Entrez la capacité de l\'enceinte'
                ]
            ])
            ->add('localisation', TextType::class, [
                'label' => 'Localisation',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                    'placeholder' => 'Entrez la localisation'
                ]
            ])
            ->add('logoFile', FileType::class, [
                'label' => 'Photo du stade',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'hidden',
                    'onchange' => 'previewLogo(this)'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stade::class,
        ]);
    }
}