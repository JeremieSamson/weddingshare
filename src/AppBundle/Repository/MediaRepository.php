<?php

namespace AppBundle\Repository;

/**
 * MediaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MediaRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $ids
     *
     * @return array
     */
    public function findByIds($ids){
        $qb = $this->createQueryBuilder('m');

        $qb->where($qb->expr()->in('m.id', $ids));

        return $qb->getQuery()->getResult();
    }
}
