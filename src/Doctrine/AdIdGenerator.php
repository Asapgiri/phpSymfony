<?php
namespace App\Doctrine;

use App\Entity\Advertisement;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

class AdIdGenerator extends AbstractIdGenerator {
    public function generate(EntityManager $em, $entity)
    {
        /**
         *   $prefix  2 digit
         *   $date    4 digit
         *   $random  3 digit
         * + $number  3 digit
         * ==================
         *   $all    12 digit
         */
        if ($entity instanceof Advertisement) {
            $prefix = "MI";
            $date = (new \DateTime())->format('yH');
            $random = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTXYZ", 3)), 0, 3);

            $numberOnDay = $em->createQueryBuilder()
                ->select('count(ad.ad_id)')
                ->from(Advertisement::class, 'ad')
                ->where('ad.ad_datetime > :time')
                ->setParameter('time', (new \DateTime())->setTime(0,0, 0))
                ->getQuery()
                ->getSingleScalarResult() + 1;
            if ($numberOnDay < 100 && $numberOnDay > 9) $numberOnDay = "0".$numberOnDay;
            elseif ($numberOnDay < 10) $numberOnDay = "00".$numberOnDay;

            return $prefix.$date.$random.$numberOnDay;
        }

        return false;
    }
}