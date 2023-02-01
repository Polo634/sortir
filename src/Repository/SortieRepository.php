<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Models\Filtre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\Env\Response;


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
    public function findSearch(Filtre $filtre): array
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
        if (!empty($filtre->firstDate)&&($filtre->lastDate)){
            $queryBuilder
                ->where('s.dateHeureDebut BETWEEN :firstDate AND :lastDate')
                ->setParameter('firstDate', $filtre->firstDate->format('Y-m-d') . ' 00:00:00')
                ->setParameter('lastDate', $filtre->lastDate->format('Y-m-d') . ' 23:59:59');
        }elseif (!empty($filtre->firstDate) && empty($filtre->lastDate)){
            $queryBuilder
                ->where('s.dateHeureDebut BETWEEN :firstDate AND :lastDate')
                ->setParameter('firstDate', $filtre->firstDate->format('Y-m-d') . ' 00:00:00')
                ->setParameter('lastDate', new \DateTime('2030-12-31'));
        }

        return $queryBuilder
            ->getQuery()->getResult();
    }


}
