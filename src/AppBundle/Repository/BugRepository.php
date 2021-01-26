<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class BugRepository extends EntityRepository
{
    public function getRecentBugsQuery()
    {
        $dql = "SELECT b, e, r FROM AppBundle:Bug b " .
               "JOIN b.engineer e JOIN b.reporter r " .
               "ORDER BY b.created DESC";

        return $this->getEntityManager()->createQuery($dql);
    }

    public function getRecentBugsArrayQuery()
    {
        $dql = "SELECT b, e, r, p FROM AppBundle:Bug b " .
               "JOIN b.engineer e JOIN b.reporter r JOIN b.products p " .
               "ORDER BY b.created DESC";

        return $this->getEntityManager()->createQuery($dql)
                    ->setHydrationMode(Query::HYDRATE_ARRAY);
    }

    public function getUsersBugsQuery($userId)
    {
        $dql = "SELECT b, e, r FROM AppBundle:Bug b " .
               "JOIN b.engineer e JOIN b.reporter r " .
               "WHERE b.status = 'OPEN' AND e.id = ?1 OR r.id = ?1 " .
               "ORDER BY b.created DESC";

        return $this->getEntityManager()->createQuery($dql)
                    ->setParameter(1, $userId);
    }

    public function getOpenBugsByProductQuery()
    {
        $dql = "SELECT p.id, p.name, count(b.id) AS openBugs FROM AppBundle:Bug b ".
               "JOIN b.products p WHERE b.status = 'OPEN' GROUP BY p.id";

        return $this->getEntityManager()->createQuery($dql)
                    ->setHydrationMode(Query::HYDRATE_SCALAR);
    }
}