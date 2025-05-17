<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\MatchFootball;
use App\Entity\Stade;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MatchFootballType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateEtHeure', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotNull(['message' => 'La date et heure sont obligatoires']),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date du match ne peut pas être dans le passé'
                    ]),
                ],
            ])
            ->add('equipe1', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new Assert\NotNull(['message' => 'L\'équipe à domicile est obligatoire']),
                    new Assert\NotEqualTo([
                        'propertyPath' => 'parent.all[equipe2].data',
                        'message' => 'Les équipes doivent être différentes'
                    ]),
                ],
                'placeholder' => 'Sélectionner une équipe',
            ])
            ->add('equipe2', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new Assert\NotNull(['message' => 'L\'équipe visiteur est obligatoire']),
                    new Assert\NotEqualTo([
                        'propertyPath' => 'parent.all[equipe1].data',
                        'message' => 'Les équipes doivent être différentes'
                    ]),
                ],
                'placeholder' => 'Sélectionner une équipe',
            ])
            ->add('stade', EntityType::class, [
                'class' => Stade::class,
                'choice_label' => 'nom',
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le stade est obligatoire']),
                ],
                'placeholder' => 'Sélectionner un stade',
            ])
            ->add('nbrBilletsVirage', IntegerType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le nombre de billets est obligatoire']),
                    new Assert\PositiveOrZero(['message' => 'Le nombre doit être positif']),
                ],
            ])
            ->add('prixBilletVirage', NumberType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le prix est obligatoire']),
                    new Assert\PositiveOrZero(['message' => 'Le prix doit être positif']),
                ],
            ])
            ->add('nbrBilletsPelouse', IntegerType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le nombre de billets est obligatoire']),
                    new Assert\PositiveOrZero(['message' => 'Le nombre doit être positif']),
                ],
            ])
            ->add('prixBilletPelouse', NumberType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le prix est obligatoire']),
                    new Assert\PositiveOrZero(['message' => 'Le prix doit être positif']),
                ],
            ])
            ->add('nbrBilletsEnceinte', IntegerType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le nombre de billets est obligatoire']),
                    new Assert\PositiveOrZero(['message' => 'Le nombre doit être positif']),
                ],
            ])
            ->add('prixBilletEnceinte', NumberType::class, [
                'constraints' => [
                    new Assert\NotNull(['message' => 'Le prix est obligatoire']),
                    new Assert\PositiveOrZero(['message' => 'Le prix doit être positif']),
                ],
            ]);

        // Ajoutez la validation personnalisée
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $match = $event->getData();

            if (!$match instanceof MatchFootball) {
                return;
            }

            $stade = $match->getStade();

            // Validation des capacités
            if ($stade) {
                if ($match->getNbrBilletsVirage() > $stade->getCapaciteVirage()) {
                    $form->get('nbrBilletsVirage')->addError(new FormError('Le nombre de billets virage dépasse la capacité du stade'));
                }

                if ($match->getNbrBilletsPelouse() > $stade->getCapacitePelouse()) {
                    $form->get('nbrBilletsPelouse')->addError(new FormError('Le nombre de billets pelouse dépasse la capacité du stade'));
                }

                if ($match->getNbrBilletsEnceinte() > $stade->getCapaciteEnceinte()) {
                    $form->get('nbrBilletsEnceinte')->addError(new FormError('Le nombre de billets enceinte dépasse la capacité du stade'));
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatchFootball::class,
        ]);
    }
}