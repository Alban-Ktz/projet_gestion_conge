<?php

namespace App\Controller;

use App\Entity\Conge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RessourcesHumainesController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/ressources-humaines/confirmer/{id}', name: 'app_rh_confirmer')]
    public function confirmerConge(Conge $conge): Response
    {
        $conge->setStatut('Confirmé');
        $this->em->persist($conge);
        $this->em->flush();
        $this->addFlash('success_confirmer','La demande de congé de ' . $conge->getDemandeur()->getFullName() . ' a bien été confirmé');
        return $this->redirectToRoute('app_tableau_de_bord');
    }
}
