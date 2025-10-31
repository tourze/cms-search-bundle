<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Tourze\CmsSearchBundle\Entity\SearchLog;

class SearchLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $searchTerms = ['篮球', '足球', '运动', '健身'];

        foreach ($searchTerms as $term) {
            $searchLog = new SearchLog();
            $searchLog->setKeyword($term);
            $searchLog->setMemberId(1);
            $searchLog->setCategoryId(0);
            $searchLog->setTopicId(0);
            $searchLog->setCount(rand(1, 10));
            $searchLog->setHit(rand(5, 50));
            $manager->persist($searchLog);
        }

        $manager->flush();
    }
}
