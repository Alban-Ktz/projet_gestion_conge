<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Conge;
use App\Services\NombreJourService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CongeCrudController extends AbstractCrudController
{

    public function __construct(
        private TranslatorInterface $translator,
        private NombreJourService $nbJour
    ) {}


    public static function getEntityFqcn(): string
    {
        return Conge::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('demandeur'),
            DateField::new('dateDebut', $this->translator->trans('Date de début')),
            DateField::new('dateFin', $this->translator->trans('Date de fin')),
            ChoiceField::new('periodeDebut', $this->translator->trans('Période de début'))
                ->setChoices([
                    $this->translator->trans('Toute la journée') => 'Toute la journée',
                    $this->translator->trans('Matin') => 'Matin',
                    $this->translator->trans('Après-midi') => 'Après-midi',
                ]),
            ChoiceField::new('periodeFin', $this->translator->trans('Période de fin'))
                ->setChoices([
                    $this->translator->trans('Toute la journée') => 'Toute la journée',
                    $this->translator->trans('Matin') => 'Matin',
                ]),
            ChoiceField::new('type', $this->translator->trans('Type'))
                ->setChoices([
                    $this->translator->trans('congeType.payes') => 'Congés payés',
                    $this->translator->trans('congeType.exceptionnels') => 'Congés exceptionnels',
                    $this->translator->trans('congeType.soldes') => 'Congés sans soldes',
                ]),
            ChoiceField::new('motif',$this->translator->trans('Motif'))
                ->setChoices([
                    $this->translator->trans('congeMotif.none') => 'Non précisé',
                    $this->translator->trans('congeMotif.mariage') => 'Mariage / PACS',
                    $this->translator->trans('congeMotif.demenagement') => 'Déménagement',
                    $this->translator->trans('congeMotif.enfant') => 'Garde d\'enfant malade',
                    $this->translator->trans('congeMotif.deces') => 'Décès',
                ])
                ->setHelp('Veuillez remplir ce champ, si le type de congés est exceptionnel'),
            IntegerField::new('nbJour', $this->translator->trans('Nb Jour'))
                ->hideOnForm(),
            ChoiceField::new('statut', $this->translator->trans('Statut'))
                ->setChoices([
                    $this->translator->trans('attente') => 'En attente',
                    $this->translator->trans('accepte') => 'Accepté',
                    $this->translator->trans('refuse') => 'Refusé',
                    $this->translator->trans('confirmé') => 'Confirmé',
                ]),
            AssociationField::new('validated_by', 'Validé par')
            ->hideOnForm(),
            DateField::new('reply_at', 'Le')
            ->hideOnForm(),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('demandeur')
            ->add(ChoiceFilter::new('statut')
                ->setChoices([
                    'En attente' => 'En attente',
                    'Accepté' => 'Accepté',
                    'Refusé' => 'Refusé',
                    'Confirmé' => 'Confirmé',
                ]))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $conge): void
    {
        $this->congeConfiguration($conge);
        parent::persistEntity($entityManager, $conge);
    } 

    public function updateEntity(EntityManagerInterface $entityManager, $conge): void
    {
        $this->congeConfiguration($conge); 
        parent::updateEntity($entityManager, $conge); 
    }

    private function congeConfiguration(Conge $conge) {
        $conge->setNbJour(($this->nbJour->getNombreJourConge($conge->getDateDebut(), $conge->getPeriodeDebut(), $conge->getDateFin(), $conge->getPeriodeFin())));

        if($conge->getStatut() != 'En attente') {
            $conge->setValidatedBy($this->getUser());
            if($conge->getStatut() != 'Confirmé') {
                $conge->setReplyAt(new DateTime());
            }
        } else {
            $conge->setValidatedBy(null);
            $conge->setReplyAt(null);
        }

    }
}
