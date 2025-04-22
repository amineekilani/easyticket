<?php

namespace App\Form;

use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'équipe',
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                    'placeholder' => 'Entrez le nom ...'
                ]
            ])
            ->add('pays', ChoiceType::class, [
                'label' => 'Pays',
                'choices' => [
                    'Tunisie' => 'Tunisie',
                    'France' => 'France',
                    'Espagne' => 'Espagne',
                    'Angleterre' => 'Angleterre',
                    'Allemagne' => 'Allemagne',
                    'Italie' => 'Italie',
                ],
                'attr' => [
                    'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500'
                ]
            ])
            ->add('logoFile', FileType::class, [
                'label' => 'Logo de l\'équipe',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'hidden',
                    'onchange' => 'previewLogo(this)'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif' => 'Actif',
                    'Archivé' => 'Archivé',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'mt-2 space-y-2'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
