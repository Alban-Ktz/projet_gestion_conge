<?php

namespace App\Repository;

use App\Entity\Conge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conge>
 *
 * @method Conge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conge[]    findAll()
 * @method Conge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CongeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conge::class);
    }

    public function add(Conge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // /mes-conges : Congés en cours et à venir

    /**
    * @return Conge[] Returns an array of Conge objects
    */
    public function findCongeEnCoursAVenir(int $user): array
    {
            $qb = $this->createQueryBuilder('c')
                ->setParameter('accepte', 'Accepté')
                ->setParameter('confirme', 'Confirmé')
                ->setParameter('refuse', 'Refusé')
                ->setParameter('user', $user)
                ->where('c.demandeur = :user')
                ->andWhere('c.statut = :accepte OR c.statut = :refuse OR c.statut =:confirme') 
                ->andWhere('c.dateFin >= CURRENT_DATE() OR c.dateDebut >= CURRENT_DATE()')
                ->orderBy('c.dateDebut', 'ASC');

            $query = $qb->getQuery()->getResult();
            return $query;

    }

    // /mes-conges : Congés passés

    /**
    * @return Conge[] Returns an array of Conge objects
    */
    public function findCongePasse(int $user): array
    {
         $qb = $this->createQueryBuilder('c')
             ->setParameter('accepte', 'Accepté')
             ->setParameter('confirme', 'Confirmé')
             ->setParameter('refuse', 'Refusé')
             ->setParameter('date', 'ADDDATE(CURRENT_DATE(), INTERVAL -12MONTH)')
             ->setParameter('user', $user)
             ->where('c.demandeur = :user')
             ->andWhere('c.statut = :accepte OR c.statut = :refuse OR c.statut =:confirme') 
             ->andWhere('c.dateDebut < CURRENT_DATE() AND c.dateDebut > :date')
             ->andWhere('(c.dateFin IS NULL) OR (c.dateFin < CURRENT_DATE())')
             ->orderBy('c.dateDebut', 'DESC');
 
         $query = $qb->getQuery()->getResult();
         return $query;
 
    }

    // /tableau-de-bord (responsable): Congés en attente de son équipe

    /**
    * @return Conge[] Returns an array of Conge objects
    */
    public function findCongeAttenteByDemandeur(int $user): array
    {
         $qb = $this->createQueryBuilder('c')
             ->setParameter('attente', 'En attente')
             ->setParameter('user', $user)
             ->where('c.demandeur = :user')
             ->andWhere('c.statut = :attente') 
             ->orderBy('c.dateDebut', 'ASC');
 
         $query = $qb->getQuery()->getResult();
         return $query;
    }

    // /tableau-de-bord (responsable): Congés en cours et à venir de son équipe

    /**
    * @return Conge[] Returns an array of Conge objects
    */
    public function findCongeEnCoursAVenirEquipe(int $user): array
    {
         $qb = $this->createQueryBuilder('c')
             ->setParameter('accepte', 'Accepté')
             ->setParameter('confirme', 'Confirmé')
             ->setParameter('user', $user)
             ->where('c.demandeur = :user')
             ->andWhere('c.statut = :confirme OR c.statut = :accepte') 
             ->andWhere('c.dateFin >= CURRENT_DATE() OR c.dateDebut >= CURRENT_DATE()')
             ->orderBy('c.dateDebut', 'ASC');
 
         $query = $qb->getQuery()->getResult();
         return $query;
    }

    // /tableau-de-bord (ressources-humaines): Congés confirmés

    /**
    * @return Conge[] Returns an array of Conge objects
    */
    public function findCongeConfirme(): array
    {
         $qb = $this->createQueryBuilder('c')
             ->setParameter('confirme', 'Confirmé')
             ->setParameter('date', 'ADDDATE(CURRENT_DATE(), INTERVAL -24 MONTH)')
             ->andWhere('c.statut = :confirme') 
             ->andWhere('c.dateDebut >= :date')
             ->orderBy('c.dateDebut', 'DESC');
 
         $query = $qb->getQuery()->getResult();
         return $query;
    }

}
