<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Company;
use App\Entity\JobOffer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobOffer>
 *
 * @method JobOffer|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobOffer|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobOffer[]    findAll()
 * @method JobOffer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobOfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }

    public function add(JobOffer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(JobOffer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByRequest(array $filters, array $orders)
    {
        $qb = $this->createQueryBuilder('j')
            ->innerJoin(
                Category::class,
                'category',
                Join::WITH,
                'j.category = category.id'
            )
            ->innerJoin(
                Company::class,
                'company',
                Join::WITH,
                'j.company = company.id'
            );

        foreach ($filters as $key => $val) {
            if (empty($val)) {
                continue;
            }

            switch ($key) {
                case 'job_offer':
                    $qb->andWhere('j.name LIKE :job_name')
                        ->setParameter('job_name', "%$val%");
                    break;
                case 'category_id':
                    $qb->andWhere('category.id = :category_id')
                        ->setParameter('category_id', $val);
                    break;
                case 'company_id':
                    $qb->andWhere('company.id = :company_id')
                        ->setParameter('company_id', $val);
                    break;
            }
        }

        foreach ($orders as $key => $order) {
            if ($order['direction'] == 'default') {
                //  $order['direction'] = 'asc';
                continue;
            }

            switch ($key) {
                case 'job_offer':
                    $qb->addOrderBy('j. name', $order['direction']);
                    break;
                case 'category':
                    $qb->addOrderBy('category.name', $order['direction']);
                    break;
                case 'company':
                    $qb->addOrderBy('company.name', $order['direction']);
                    break;
            }
        }

        return $qb->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return JobOffer[] Returns an array of JobOffer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('j.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?JobOffer
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
