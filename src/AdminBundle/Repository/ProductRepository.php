<?php

namespace AdminBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{
    /**
     * return products for admin
     *
     * @return QueryBuilder
     */
    public function getAllProductsAdmin()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(['p', 'pc', 'pfe'])
            ->from('AdminBundle:Product', 'p')
            ->leftJoin('p.categories', 'pc')
            ->leftJoin('p.featured', 'pfe')
            ->where($qb->expr()->neq('p.deleted', 1));

        return $qb;
    }

    /**
     * return products for admin
     *
     * @param string $searchWords
     * @return QueryBuilder
     */
    public function searchProductsAdmin($searchWords)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p', 'pc')
            ->from('AdminBundle:Product', 'p')
            ->leftJoin('p.categories', 'pc');

        $searchWords = explode(' ', $searchWords);
        $cqbORX = [];

        foreach ($searchWords as $searchWord) {
            $cqbORX[] = $qb->expr()->like('p.name', $qb->expr()->literal('%' . $searchWord . '%'));
            $cqbORX[] = $qb->expr()->like('p.description', $qb->expr()->literal('%' . $searchWord . '%'));
        }

        $qb->andWhere(call_user_func_array([$qb->expr(), "orx"], $cqbORX));

        return $qb;
    }
}
