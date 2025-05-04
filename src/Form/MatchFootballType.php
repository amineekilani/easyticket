<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\MatchFootball;
use App\Entity\Stade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchFootballType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateEtHeure', null, [
                'widget' => 'single_text'
            ])
            ->add('nbrBilletsVirage')
            ->add('prixBilletVirage')
            ->add('nbrBilletsPelouse')
            ->add('prixBilletPelouse')
            ->add('nbrBilletsEnceinte')
            ->add('prixBilletEnceinte')
            ->add('equipe1', EntityType::class, [
                'class' => Equipe::class,
'choice_label' => 'id',
            ])
            ->add('equipe2', EntityType::class, [
                'class' => Equipe::class,
'choice_label' => 'id',
            ])
            ->add('stade', EntityType::class, [
                'class' => Stade::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatchFootball::class,
        ]);
    }
}
