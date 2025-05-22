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
                
                // Vérification de disponibilité du stade
                $dateMatch = $match->getDateEtHeure();
                if ($dateMatch) {
                    $em = $form->getConfig()->getOption('entity_manager');
                    
                    // Créer un intervalle de temps pour le jour entier
                    $debutJour = (clone $dateMatch)->setTime(0, 0, 0);
                    $finJour = (clone $dateMatch)->setTime(23, 59, 59);
                    
                    $qb = $em->createQueryBuilder()
                        ->select('COUNT(m)')
                        ->from('App\Entity\MatchFootball', 'm')
                        ->where('m.stade = :stade')
                        ->andWhere('m.dateEtHeure BETWEEN :debut AND :fin')
                        ->setParameter('stade', $stade)
                        ->setParameter('debut', $debutJour)
                        ->setParameter('fin', $finJour);
                    
                    // Exclure le match actuel en cas de modification
                    if ($match->getId()) {
                        $qb->andWhere('m.id != :id')
                           ->setParameter('id', $match->getId());
                    }
                    
                    $matchesCount = $qb->getQuery()->getSingleScalarResult();
                    
                    if ($matchesCount > 0) {
                        $form->get('stade')->addError(
                            new FormError('Ce stade est déjà réservé pour un autre match ce jour-là')
                        );
                        $form->get('dateEtHeure')->addError(
                            new FormError('Une autre rencontre est déjà programmée dans ce stade à cette date')
                        );
                    }
                    
                    // Vérification pour l'équipe 1
                    $equipe1 = $match->getEquipe1();
                    if ($equipe1) {
                        $qb1 = $em->createQueryBuilder()
                            ->select('COUNT(m)')
                            ->from('App\Entity\MatchFootball', 'm')
                            ->where('(m.equipe1 = :equipe OR m.equipe2 = :equipe)')
                            ->andWhere('m.dateEtHeure BETWEEN :debut AND :fin')
                            ->setParameter('equipe', $equipe1)
                            ->setParameter('debut', $debutJour)
                            ->setParameter('fin', $finJour);
                        
                        // Exclure le match actuel en cas de modification
                        if ($match->getId()) {
                            $qb1->andWhere('m.id != :id')
                               ->setParameter('id', $match->getId());
                        }
                        
                        $equipe1MatchesCount = $qb1->getQuery()->getSingleScalarResult();
                        
                        if ($equipe1MatchesCount > 0) {
                            $form->get('equipe1')->addError(
                                new FormError('Cette équipe a déjà un match programmé ce jour-là')
                            );
                            $form->get('dateEtHeure')->addError(
                                new FormError('Une des équipes a déjà un match programmé à cette date')
                            );
                        }
                    }
                    
                    // Vérification pour l'équipe 2
                    $equipe2 = $match->getEquipe2();
                    if ($equipe2) {
                        $qb2 = $em->createQueryBuilder()
                            ->select('COUNT(m)')
                            ->from('App\Entity\MatchFootball', 'm')
                            ->where('(m.equipe1 = :equipe OR m.equipe2 = :equipe)')
                            ->andWhere('m.dateEtHeure BETWEEN :debut AND :fin')
                            ->setParameter('equipe', $equipe2)
                            ->setParameter('debut', $debutJour)
                            ->setParameter('fin', $finJour);
                        
                        // Exclure le match actuel en cas de modification
                        if ($match->getId()) {
                            $qb2->andWhere('m.id != :id')
                               ->setParameter('id', $match->getId());
                        }
                        
                        $equipe2MatchesCount = $qb2->getQuery()->getSingleScalarResult();
                        
                        if ($equipe2MatchesCount > 0) {
                            $form->get('equipe2')->addError(
                                new FormError('Cette équipe a déjà un match programmé ce jour-là')
                            );
                            if (!$form->get('dateEtHeure')->getErrors()->count()) {
                                $form->get('dateEtHeure')->addError(
                                    new FormError('Une des équipes a déjà un match programmé à cette date')
                                );
                            }
                        }
                    }
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MatchFootball::class,
            'entity_manager' => null,
        ]);
    }
}