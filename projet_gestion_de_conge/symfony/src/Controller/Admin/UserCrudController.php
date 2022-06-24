<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private TranslatorInterface $translator
        ) {}

    public static function getEntityFqcn(): string
    {
        return User::class;
    } 

    public function configureFields(string $pageName): iterable
    {
        $result = [];

        $result = [
            EmailField::new('email', $this->translator->trans('Identifiant')), 
            TextField::new('prenom'), 
            TextField::new('nom'),
        ];

        if($pageName === 'new') {
            $result[] = TextField::new('password')->setRequired(true)->setFormType(PasswordType::class)->onlyOnForms();
        } else if ($pageName === 'edit') {
            $result[] = TextField::new('newPassword')->setRequired(false)->setFormType(PasswordType::class)->onlyOnForms();
        }

        array_push(
            $result,  
        ChoiceField::new('roles')
            ->setChoices([
                $this->translator->trans('Demandeur') => 'ROLE_DEMANDEUR',
                $this->translator->trans('Ressources Humaines') => 'ROLE_RH',
                $this->translator->trans('Responsable') => 'ROLE_RESPONSABLE',
                $this->translator->trans('Admin') => 'ROLE_ADMIN'])
            ->onlyOnForms()
            ->allowMultipleChoices(),
        BooleanField::new('isadmin', $this->translator->trans('Admin'))
            ->hideOnForm()
            ->renderAsSwitch(false),
        BooleanField::new('isresponsable', $this->translator->trans('Responsable'))
            ->hideOnForm()
            ->renderAsSwitch(false),
        BooleanField::new('isrh', $this->translator->trans('Ressources Humaines'))
            ->hideOnForm()
            ->renderAsSwitch(false),
        AssociationField::new('responsable', 'Responsables')
            ->setFormTypeOption('by_reference', false)
            ->onlyOnForms(),
        AssociationField::new('demandeurs')
            ->onlyOnForms(),
        ArrayField::new('getresponsable', $this->translator->trans('Liste des responsables'))
            ->hideOnForm(),
        );
        return $result;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email')
            ->add('responsable')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setDefaultSort([
                'roles' => 'ASC',
            ]);
    }
    
    public function persistEntity(EntityManagerInterface $entityManager, $user): void
    {
        if ($user->getPassword() != null) {
            $hashedPassword = $this->passwordHasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hashedPassword);
        }
        parent::persistEntity($entityManager, $user);
    } 

    public function updateEntity(EntityManagerInterface $entityManager, $user): void
    {
        if ($user->getNewPassword() != null) {
            $hashedPassword = $this->passwordHasher->hashPassword($user,$user->getNewPassword());
            $user->setPassword($hashedPassword);
        }
        parent::updateEntity($entityManager, $user); 
    }

}
