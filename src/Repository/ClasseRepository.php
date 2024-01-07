<?php

namespace App\Repository;

use App\Entity\Classe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Classe>
 *
 * @method Classe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Classe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Classe[]    findAll()
 * @method Classe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClasseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classe::class);
    }

    public function add(Classe $entity, bool $flush = false): void
    {
        $entityManager=$this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
        //$this->getEntityManager()->persist($entity);

        /*if ($flush) {
            $this->getEntityManager()->flush();
        }*/
    }

    public function remove(Classe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Classe[] Returns an array of Classe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Classe
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function getOneClasse($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id='.$id)
            ->getQuery()
            ->getArrayResult()
        ;
    }

    public function findClasseByIdEnseignant($id){
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT e
            FROM App\Entity\Enseignant e, App\Entity\Classe c
            WHERE c.id_enseignant = e.id and c.id_enseignant= :id
            '
        )->setParameter('id', $id);

        // returns an array of Product objects
        return $query->getResult();
    
    }
}
