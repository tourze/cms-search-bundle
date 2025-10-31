<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsSearchBundle\Entity\SearchLog;
use Tourze\CmsSearchBundle\Repository\SearchLogRepository;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(SearchLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class SearchLogRepositoryTest extends AbstractRepositoryTestCase
{
    private SearchLogRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = $this->getRepository();
    }

    protected function createNewEntity(): SearchLog
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(1);
        $searchLog->setKeyword('test keyword');
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(1);
        $searchLog->setHit(0);

        return $searchLog;
    }

    protected function getRepository(): SearchLogRepository
    {
        return self::getService(SearchLogRepository::class);
    }

    public function testRepositoryConstruction(): void
    {
        $repository = $this->getRepository();

        $this->assertInstanceOf(ServiceEntityRepository::class, $repository);
        $this->assertInstanceOf(SearchLogRepository::class, $repository);
    }

    /**
     * 测试保存搜索日志.
     */
    public function testSaveSearchLog(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(123);
        $searchLog->setKeyword('test search');
        $searchLog->setCategoryId(1);
        $searchLog->setTopicId(2);
        $searchLog->setCount(1);
        $searchLog->setHit(5);

        $this->repository->save($searchLog);

        $this->assertNotNull($searchLog->getId());

        // 验证搜索日志已保存
        $savedSearchLog = $this->repository->find($searchLog->getId());
        $this->assertInstanceOf(SearchLog::class, $savedSearchLog);
        $this->assertSame(123, $savedSearchLog->getMemberId());
        $this->assertSame('test search', $savedSearchLog->getKeyword());
        $this->assertSame(1, $savedSearchLog->getCategoryId());
        $this->assertSame(2, $savedSearchLog->getTopicId());
        $this->assertSame(1, $savedSearchLog->getCount());
        $this->assertSame(5, $savedSearchLog->getHit());
    }

    /**
     * 测试保存搜索日志但不刷新.
     */
    public function testSaveSearchLogWithoutFlush(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(456);
        $searchLog->setKeyword('no flush test');
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(2);
        $searchLog->setHit(10);

        $this->repository->save($searchLog, false);

        // 手动刷新
        $em = self::getEntityManager();
        $em->flush();

        $this->assertNotNull($searchLog->getId());
    }

    /**
     * 测试删除搜索日志.
     */
    public function testRemoveSearchLog(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(789);
        $searchLog->setKeyword('to be removed');
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(1);
        $searchLog->setHit(0);
        $this->repository->save($searchLog);

        $searchLogId = $searchLog->getId();
        $this->repository->remove($searchLog);

        // 验证搜索日志已删除
        $deletedSearchLog = $this->repository->find($searchLogId);
        $this->assertNull($deletedSearchLog);
    }

    /**
     * 测试删除搜索日志但不刷新.
     */
    public function testRemoveSearchLogWithoutFlush(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(321);
        $searchLog->setKeyword('remove no flush');
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(1);
        $searchLog->setHit(0);
        $this->repository->save($searchLog);

        $searchLogId = $searchLog->getId();
        $this->repository->remove($searchLog, false);

        // 手动刷新
        $em = self::getEntityManager();
        $em->flush();

        // 验证搜索日志已删除
        $deletedSearchLog = $this->repository->find($searchLogId);
        $this->assertNull($deletedSearchLog);
    }

    /**
     * 测试通过ID查找搜索日志.
     */
    public function testFindSearchLogById(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(555);
        $searchLog->setKeyword('find by id test');
        $searchLog->setCategoryId(3);
        $searchLog->setTopicId(4);
        $searchLog->setCount(3);
        $searchLog->setHit(15);
        $this->repository->save($searchLog);

        $result = $this->repository->find($searchLog->getId());

        $this->assertInstanceOf(SearchLog::class, $result);
        $this->assertSame($searchLog->getId(), $result->getId());
        $this->assertSame('find by id test', $result->getKeyword());
    }

    /**
     * 测试查找不存在的搜索日志.
     */
    public function testFindNonExistentSearchLog(): void
    {
        $result = $this->repository->find(-999);

        $this->assertNull($result);
    }

    /**
     * 测试查找所有搜索日志.
     */
    public function testFindAllSearchLogs(): void
    {
        $searchLog1 = new SearchLog();
        $searchLog1->setMemberId(111);
        $searchLog1->setKeyword('test all 1');
        $searchLog1->setCategoryId(0);
        $searchLog1->setTopicId(0);
        $searchLog1->setCount(1);
        $searchLog1->setHit(0);
        $this->repository->save($searchLog1);

        $searchLog2 = new SearchLog();
        $searchLog2->setMemberId(222);
        $searchLog2->setKeyword('test all 2');
        $searchLog2->setCategoryId(0);
        $searchLog2->setTopicId(0);
        $searchLog2->setCount(1);
        $searchLog2->setHit(0);
        $this->repository->save($searchLog2);

        $result = $this->repository->findAll();

        $this->assertGreaterThanOrEqual(2, \count($result));
    }

    /**
     * 测试通过条件查找单个搜索日志.
     */
    public function testFindOneByCondition(): void
    {
        $keyword = 'unique_keyword_' . uniqid();
        $searchLog = new SearchLog();
        $searchLog->setMemberId(333);
        $searchLog->setKeyword($keyword);
        $searchLog->setCategoryId(5);
        $searchLog->setTopicId(6);
        $searchLog->setCount(2);
        $searchLog->setHit(8);
        $this->repository->save($searchLog);

        $result = $this->repository->findOneBy(['keyword' => $keyword]);

        $this->assertInstanceOf(SearchLog::class, $result);
        $this->assertSame($keyword, $result->getKeyword());
        $this->assertSame(333, $result->getMemberId());
    }

    /**
     * 测试通过条件查找多个搜索日志.
     */
    public function testFindByCondition(): void
    {
        $memberId = 777;

        $searchLog1 = new SearchLog();
        $searchLog1->setMemberId($memberId);
        $searchLog1->setKeyword('member search 1');
        $searchLog1->setCategoryId(0);
        $searchLog1->setTopicId(0);
        $searchLog1->setCount(1);
        $searchLog1->setHit(0);
        $this->repository->save($searchLog1);

        $searchLog2 = new SearchLog();
        $searchLog2->setMemberId($memberId);
        $searchLog2->setKeyword('member search 2');
        $searchLog2->setCategoryId(0);
        $searchLog2->setTopicId(0);
        $searchLog2->setCount(1);
        $searchLog2->setHit(0);
        $this->repository->save($searchLog2);

        $result = $this->repository->findBy(['memberId' => $memberId]);

        $this->assertGreaterThanOrEqual(2, \count($result));

        foreach ($result as $log) {
            $this->assertSame($memberId, $log->getMemberId());
        }
    }

    /**
     * 测试通过条件查找并排序.
     */

    /**
     * 测试通过条件查找并分页.
     */
    public function testFindByWithLimitAndOffset(): void
    {
        // 创建多个搜索日志
        for ($i = 0; $i < 5; ++$i) {
            $searchLog = new SearchLog();
            $searchLog->setMemberId(1000 + $i);
            $searchLog->setKeyword('pagination test ' . $i);
            $searchLog->setCategoryId(2);
            $searchLog->setTopicId(0);
            $searchLog->setCount(1);
            $searchLog->setHit(0);
            $this->repository->save($searchLog);
        }

        $result = $this->repository->findBy(['categoryId' => 2], null, 2, 1);

        $this->assertLessThanOrEqual(2, \count($result));
    }

    /**
     * 测试计数功能.
     */
    public function testCountSearchLogs(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(1111);
        $searchLog->setKeyword('count test');
        $searchLog->setCategoryId(3);
        $searchLog->setTopicId(0);
        $searchLog->setCount(1);
        $searchLog->setHit(0);
        $this->repository->save($searchLog);

        $result = $this->repository->count(['categoryId' => 3]);

        $this->assertGreaterThanOrEqual(1, $result);
    }

    /**
     * 测试空条件计数.
     */
    public function testCountWithEmptyCriteria(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(1234);
        $searchLog->setKeyword('empty criteria count');
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(1);
        $searchLog->setHit(0);
        $this->repository->save($searchLog);

        $result = $this->repository->count([]);

        $this->assertGreaterThanOrEqual(1, $result);
    }

    /**
     * 测试不匹配条件的计数.
     */
    public function testCountWithNonMatchingCriteria(): void
    {
        $result = $this->repository->count(['memberId' => -9999]);

        $this->assertSame(0, $result);
    }

    /**
     * 测试通过不同字段查找.
     */
    public function testFindByDifferentFields(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(5555);
        $searchLog->setKeyword('multi field test');
        $searchLog->setCategoryId(10);
        $searchLog->setTopicId(20);
        $searchLog->setCount(5);
        $searchLog->setHit(25);
        $this->repository->save($searchLog);

        // 测试通过不同字段查找
        $resultByMember = $this->repository->findBy(['memberId' => 5555]);
        $this->assertGreaterThanOrEqual(1, \count($resultByMember));

        $resultByCategory = $this->repository->findBy(['categoryId' => 10]);
        $this->assertGreaterThanOrEqual(1, \count($resultByCategory));

        $resultByTopic = $this->repository->findBy(['topicId' => 20]);
        $this->assertGreaterThanOrEqual(1, \count($resultByTopic));

        $resultByCount = $this->repository->findBy(['count' => 5]);
        $this->assertGreaterThanOrEqual(1, \count($resultByCount));

        $resultByHit = $this->repository->findBy(['hit' => 25]);
        $this->assertGreaterThanOrEqual(1, \count($resultByHit));
    }

    /**
     * 测试复杂查询条件.
     */
    public function testFindByComplexCriteria(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(6666);
        $searchLog->setKeyword('complex test');
        $searchLog->setCategoryId(15);
        $searchLog->setTopicId(25);
        $searchLog->setCount(3);
        $searchLog->setHit(9);
        $this->repository->save($searchLog);

        $result = $this->repository->findBy([
            'memberId' => 6666,
            'categoryId' => 15,
            'topicId' => 25,
        ]);

        $this->assertGreaterThanOrEqual(1, \count($result));

        $foundLog = $result[0];
        $this->assertSame(6666, $foundLog->getMemberId());
        $this->assertSame(15, $foundLog->getCategoryId());
        $this->assertSame(25, $foundLog->getTopicId());
    }

    /**
     * 测试实体的字符串表示.
     */
    public function testEntityToString(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(7777);
        $searchLog->setKeyword('string test');
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(1);
        $searchLog->setHit(0);
        $this->repository->save($searchLog);

        $stringRepresentation = (string) $searchLog;
        $this->assertStringContainsString('string test', $stringRepresentation);
        $this->assertStringContainsString((string) $searchLog->getId(), $stringRepresentation);
    }

    /**
     * 测试实体的API数组表示.
     */
    public function testEntityRetrieveApiArray(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setMemberId(8888);
        $searchLog->setKeyword('api array test');
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(2);
        $searchLog->setHit(0);
        $this->repository->save($searchLog);

        $apiArray = $searchLog->retrieveApiArray();

        $this->assertArrayHasKey('id', $apiArray);
        $this->assertArrayHasKey('keyword', $apiArray);
        $this->assertArrayHasKey('count', $apiArray);
        $this->assertSame($searchLog->getId(), $apiArray['id']);
        $this->assertSame('api array test', $apiArray['keyword']);
        $this->assertSame(2, $apiArray['count']);
    }

    // PHPStan 要求的标准测试用例
}
