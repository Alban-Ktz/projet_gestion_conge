<?php

namespace App\Controller;

use DateTime;
use App\Entity\Conge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResponsableController extends AbstractController
{

    public function __construct(public EntityManagerInterface $em) {}

    #[Route('/responsable/approuver/{id}', name: 'app_responsable_approuver')]
    public function accepterDemande(Conge $conge): Response
    {
        $this->denyAccessUnlessGranted('CONGE_ACCEPT', $conge);
        $conge->setValidatedBy($this->getUser());
        $conge->setReplyAt(new DateTime());

        $conge->setStatut('Accepté');
        $this->em->persist($conge);
        $this->em->flush();

        $this->addFlash('success_accept','La demande de congé de ' . $conge->getDemandeur()->getFullName() . ' a bien été accepté');
        return $this->redirectToRoute('app_tableau_de_bord');
    }

    #[Route('/responsable/decliner/{id}', name: 'app_responsable_decliner')]
    public function refuserDemande(Conge $conge): Response
    {
        $this->denyAccessUnlessGranted('CONGE_DECLINE', $conge);
        $conge->setValidatedBy($this->getUser());
        $conge->setReplyAt(new DateTime());


        $conge->setStatut('Refusé');
        $this->em->persist($conge);
        $this->em->flush();
        $this->addFlash('success_reject','La demande de congé de ' . $conge->getDemandeur()->getFullName() . ' a bien été refusé');
        return $this->redirectToRoute('app_tableau_de_bord');
    }

    
}
