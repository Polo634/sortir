<?php

namespace App\Repository;


use App\Entity\Participant;
use App\Entity\Sortie;
use App\Models\Filtre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;



/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     *  récupère les sorties en fonction des filtres
     * @return Sortie[]
     */
    public function findSearch(Filtre $filtre, Participant $user): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('s')
            ->join('s.etat', 'e')
            ->andWhere('e.libelle != \'Historisée\'');


        if (!empty($filtre->campus)){
            $queryBuilder
                ->andWhere('s.campus = :campus')
                ->setParameter('campus' , $filtre->campus);


        }

        if (!empty($filtre->q)){
           $queryBuilder
               ->andWhere('s.nom LIKE :q')
               ->setParameter('q', "%{$filtre->q}%");
        }
        if (!empty($filtre->firstDate)){
            $queryBuilder
                ->andWhere('s.dateHeureDebut >= :firstDate ')
                ->setParameter('firstDate', $filtre->firstDate);

        }

        if (!empty($filtre->lastDate)){
            $queryBuilder
                ->andwhere('s.dateHeureDebut <= :lastDate')
                ->setParameter('lastDate', $filtre->lastDate);
        }

          if ($filtre->organisateur){
            $queryBuilder
                ->andwhere('s.organisateur =:user')
                ->setParameter('user',$user);
        }
          if($filtre->inscrit){
              $queryBuilder
                  ->andwhere(':user MEMBER OF s.participants')
                  ->setParameter('user', $user);
          }
        if($filtre->pasInscrit){
            $queryBuilder
                ->andwhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);

        }

        if ($filtre->sortiesPassees){
            $queryBuilder
                ->andWhere('e.libelle = \'Passée\'');
        }


        return $queryBuilder
            ->getQuery()->getResult();
    }



}
