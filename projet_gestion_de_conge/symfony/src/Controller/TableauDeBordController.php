<?php

namespace App\Controller;

use App\Entity\Conge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TableauDeBordController extends AbstractController
{
    public function __construct(public EntityManagerInterface $em) {}

    #[Route('/tableau-de-bord', name: 'app_tableau_de_bord')]
    public function afficherDemandesEnAttente(): Response
    {
        $user = $this->getUser();
        $congesRep = $this->em->getRepository(Conge::class);
        $congesEnAttente = [];
        $congesEnCoursAVenir = [];


        foreach ($user->getDemandeurs() as $demandeur) {
            if($congesRep->findCongeAttenteByDemandeur($demandeur->getId())) {
                $congesEnAttente[] = $congesRep->findCongeAttenteByDemandeur($demandeur->getId());
            }
        }

        foreach ($user->getDemandeurs() as $demandeur) {
            if($congesRep->findCongeEnCoursAVenirEquipe($demandeur->getId())) {
                $congesEnCoursAVenir[] = $congesRep->findCongeEnCoursAVenirEquipe($demandeur->getId());
            }
        }

        $congesAConfirmer = $congesRep->findBy([
            'statut' => 'Accepté',
        ],[
            'dateDebut' => 'ASC',
        ]);

        $congesConfirme = $congesRep->findCongeConfirme();


        return $this->render('tableau_de_bord/tableau_de_bord.html.twig', [
            'congesEnAttente' => $congesEnAttente,
            'congesEnCoursAVenir' => $congesEnCoursAVenir,
            'congesAConfirmer' => $congesAConfirmer,
            'congesConfirme' => $congesConfirme
        ]);
    }
    
}
