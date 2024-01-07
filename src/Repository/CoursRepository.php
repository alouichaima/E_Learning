<?php

namespace App\Repository;

use App\Entity\Cours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Filter;

/**
 * @extends ServiceEntityRepository<Classe>
 *
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cours::class);
    }

    public function add(Cours $entity, bool $flush = false): void
    {
        $entityManager=$this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
        //$this->getEntityManager()->persist($entity);

        /*if ($flush) {
            $this->getEntityManager()->flush();
        }*/
    }

    public function remove(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /* Fonction pour récupérer toutes les notes du cours pour en faire la moyenne */
    public function getAllRatingOfTheCourse($id){
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
           
            'SELECT a.user_rating
            FROM App\Entity\Avis a, App\Entity\Cours c
            WHERE c.id = a.cours and a.cours= :id
            '
        )->setParameter('id', $id);

        // returns an array of Product objects
        return $query->getResult();
    
    }

   

   

        public function getAllApprenantInscritAuCours($id):array
    {
        $conn=$this->getEntityManager()->getConnection();
        $sql='SELECT a.prenom, a.nom, a.image, av.user_rating FROM `cours_apprenant`ca , apprenant a, avis av ,cours c WHERE ca.apprenant_id=a.id and a.avis_id=av.id and c.id=av.cours_id and ca.cours_id='.$id; 
   
        $stmt=$conn->prepare($sql);
        $result=$stmt->executeQuery();
        return $result->fetchAllAssociative();
    }
   // SELECT a.prenom, a.nom, a.image, av.user_rating FROM `cours_apprenant`ca , apprenant a , avis av,cours c WHERE ca.apprenant_id=a.id and ca.cours_id=2 and a.avis_id=av.id  and c.id=av.cours_id;  

   public function findSearch(Filter $filter): array
   {
       $query = $this
               ->createQueryBuilder('p')
               ->select('c','p')
               ->join('p.id_categorie','c');


               if(!empty($filter->mot)) {
                   $query = $query
                       ->andWhere('p.titre_cours LIKE :mot')
                       ->setParameter('mot',"%{$filter->mot}%");
               }

               if(!empty($filter->min)) {
                   $query = $query
                   ->andWhere('p.prix >= :min')
                   ->setParameter('min', $filter->min);
               }

               if(!empty($filter->max)) {
                   $query = $query
                   ->andWhere('p.prix <= :max')
                   ->setParameter('max', $filter->max);
               }

               if(!empty($filter->categorie)) {
                   $query = $query
                       ->andWhere('c.id IN (:categorie)')
                       ->setParameter('categorie',$filter->categorie);
               }


               return $query->getQuery()->getresult();
   }


}