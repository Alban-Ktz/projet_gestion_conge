<?php

namespace App\Controller;

use DateTime;
use App\Entity\Conge;
use App\Form\CongeType;
use App\Services\NombreJourService;
use App\Repository\CongeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CongeController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $em
    ) {}
    
    #[Route('/demande-de-conge', name: 'app_formulaireconge')]
    public function ajouterConge(Request $request, NombreJourService $nbJour): Response
    {
        $conge = new Conge();

        $listeDemandeur = $this->getUser()->getDemandeurFilter($this->getUser());


        $form = $this->createForm(CongeType::class, $conge, ['listeDemandeurs' => $listeDemandeur]);
        $form->handleRequest($request);

        if($this->getUser()->getDemandeurs()[0] === null) {
            $conge->setDemandeur($this->getUser());
        }

        if($form->isSubmitted() && $form->isValid()) {
            if($conge->getType() == 'Congés payés' || $conge->getType() == 'Congés sans soldes') {
                $conge->setMotif(null);
            }

            if(!$conge->getDateFin()) {
                $conge->setPeriodeFin(null);
            }



            $conge->setNbJour($nbJour->getNombreJourConge($conge->getDateDebut(), $conge->getPeriodeDebut(), $conge->getDateFin(), $conge->getPeriodeFin()));
            $conge->setStatut('En attente');
            $this->em->persist($conge);
            $this->em->flush();
            $this->addFlash('success_add','Votre congé a bien été ajouté');
            return $this->redirectToRoute('app_mesconges');
        } 
        
        return $this->renderForm('conge/formulaire.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/modifier/{id}', name: 'app_modifierconge')]
    public function modifierConge(Conge $conge, Request $request, NombreJourService $nbJour): Response
    {
        $this->denyAccessUnlessGranted('CONGE_EDIT', $conge);

        $listeDemandeur = $this->getUser()->getDemandeurFilter($this->getUser());

        $form = $this->createForm(CongeType::class, $conge, ['listeDemandeurs' => $listeDemandeur]);
        $form->handleRequest($request);

        if($this->getUser()->getDemandeurs()[0] === null) {
            $conge->setDemandeur($this->getUser());
        }

        if($form->isSubmitted() && $form->isValid()) {
            if($conge->getType() != 'Congés exceptionnels') $conge->setMotif(null);
            if(!$conge->getDateFin()) $conge->setPeriodeFin(null);

            $conge->setNbJour($nbJour->getNombreJourConge($conge->getDateDebut(), $conge->getPeriodeDebut(), $conge->getDateFin(), $conge->getPeriodeFin()));
            $conge->setValidatedBy(null);
            $conge->setStatut('En attente');
            $conge->setReplyAt(null);

            $this->em->persist($conge);
            $this->em->flush();
            if($conge->getDemandeur() == $this->getUser()) {
                $this->addFlash('success_edit','Votre congé a bien été modifié');
                return $this->redirectToRoute('app_mesconges');
            } 
            $this->addFlash('success_edit_resp','Le congé de '. $conge->getDemandeur()->getFullName() .' a bien été modifié');
            return $this->redirectToRoute('app_tableau_de_bord');
        }

        return $this->renderForm('conge/formulaire.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/supprimer/{id}', name: 'app_supprimerconge')]
    public function supprimerConge(Conge $conge): Response
    {
        $this->denyAccessUnlessGranted('CONGE_DELETE', $conge);

        $this->em->remove($conge);
        $this->em->flush();
        $this->addFlash('success_delete','Votre congé a bien été supprimé');
        return $this->redirectToRoute('app_mesconges');
    }

    #[Route('/mes-conges', name: 'app_mesconges')]
    public function afficherConges(): Response
    {
        $user = $this->getUser()->getId();
        $congesRep = $this->em->getRepository(Conge::class);

        $congesEnAttente = $congesRep->findBy(
            [
                'demandeur' => $user,
                'statut' => 'En Attente',
            ], 
            [
                'dateDebut' => 'ASC'
            ]);
        $congesAVenir = $congesRep->findCongeEnCoursAVenir($user);
        $congesPasse = $congesRep->findCongePasse($user);

        return $this->render('conge/liste_conge.html.twig', [
            'congesEnAttente' => $congesEnAttente,
            'congesAVenir' => $congesAVenir,
            'congesPasse' => $congesPasse,
        ]);
    }
}
