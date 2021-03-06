<?php

namespace AdminBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * query for admin paginator
     *
     * @return QueryBuilder
     */
    public function getAllUsersAdminQB()
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(['u', 'uo', 'ur'])
            ->from('AdminBundle:User', 'u')
            ->leftJoin('u.orders', 'uo')
            ->leftJoin('u.roles', 'ur')
            ->where('ur.name NOT LIKE :roles')
            ->setParameter('roles', '%"ROLE_ADMIN"%');

        return $qb;
    }
}
