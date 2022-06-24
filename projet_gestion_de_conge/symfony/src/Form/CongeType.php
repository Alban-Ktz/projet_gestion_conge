<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Conge;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CongeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('dateDebut',DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'dd/MM/yyyy',
                'input' => 'datetime',
            ])
            ->add('dateFin',DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'dd/MM/yyyy',
                'input' => 'datetime',
                'required' => false,
            ])
            ->add('demandeur',ChoiceType::class, [
                'choices' => $options['listeDemandeurs'],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $choicesPeriodeDebut = [
                    'Toute la journée' => 'Toute la journée',
                    'Matin' => 'Matin',
                    'Après-midi' => 'Après-midi',
                ];
                $data = $event->getData();
                $form = $event->getForm();
                if($data->getPeriodeDebut() == null) {
                    $form->add('periodeDebut',ChoiceType::class, [
                        'choices' => $choicesPeriodeDebut,
                        'expanded' => true,
                        'data' => 'Toute la journée',
                    ]);
                } else {
                    $form->add('periodeDebut',ChoiceType::class, [
                        'choices' => $choicesPeriodeDebut,
                        'expanded' => true,
                    ]);
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $choicesPeriodeFin = [
                    'Toute la journée' => 'Toute la journée',
                    'Matin' => 'Matin',
                ];
                $data = $event->getData();
                $form = $event->getForm();
                if($data->getPeriodeFin() == null) {
                    $form->add('periodeFin',ChoiceType::class, [
                        'choices' => $choicesPeriodeFin,
                        'expanded' => true,
                        'data' => 'Toute la journée',
                        'label' => false,
                    ]);
                } else {
                    $form->add('periodeFin',ChoiceType::class, [
                        'choices' => $choicesPeriodeFin,
                        'expanded' => true,
                        'label' => false,
                    ]);
                }
            })
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'congeType.payes' => 'Congés payés',
                    'congeType.exceptionnels' => 'Congés exceptionnels',
                    'congeType.soldes' => 'Congés sans soldes',
                ],
            ])
            ->add('motif', ChoiceType::class, [
                'choices' => [
                    'congeMotif.none' => 'Non Précisé',
                    'congeMotif.demenagement' => 'Déménagement',
                    'congeMotif.enfant' => 'Garde d\'enfant malade',
                    'congeMotif.mariage' => 'Mariage / PACS',
                    'congeMotif.deces' => 'Décès',
                ],
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
            'listeDemandeurs' => null,
            'csrf_protection' => false,
        ]);
    }
}
