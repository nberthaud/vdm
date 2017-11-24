<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class PostRepository
 * @package AppBundle\Repository
 */
class PostRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function clearAllPosts()
    {
        return $this
            ->getEntityManager()
            ->createQuery('DELETE AppBundle:Post p')
            ->execute()
        ;
    }

    /**
     * @param null|DateTime $from
     * @param null|DateTime $to
     * @param null|string $author
     * @internal param $data
     * @return array
     */
    public function search($from = null, $to = null, $author = null)
    {
        $qb = $this->createQueryBuilder('p');

        if ($from) {
            $qb ->andWhere('p.datetime >= :from')
                ->setParameter('from', $from, \Doctrine\DBAL\Types\Type::DATETIME);
        }

        if ($to) {
            $qb ->andWhere('p.datetime <= :to')
                ->setParameter('to', $to, \Doctrine\DBAL\Types\Type::DATETIME);
        }

        if ($author) {
            $qb ->andWhere('p.author = :author')
                ->setParameter('author', $author, \Doctrine\DBAL\Types\Type::STRING);
        }

        return $qb->getQuery()->getResult();
    }
}