<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Conge;
use App\Controller\CongeController;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\UserCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{

    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private EntityManagerInterface $em,
        private TranslatorInterface $translator
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
            ->setController(UserCrudController::class)
            ->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('AxioCode');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute($this->translator->trans('Revenir sur le site web'), 'fa fa-home', 'app_mesconges');
        yield MenuItem::linkToCrud($this->translator->trans('Utilisateur'), 'fa fa-user', User::class);
        yield MenuItem::linkToCrud($this->translator->trans('Conge'), 'fa fa-calendar', Conge::class);
    }

}
