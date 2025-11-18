<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsSearchBundle\Controller\Admin\CmsSearchLogCrudController;
use Tourze\CmsSearchBundle\Entity\SearchLog;
use Tourze\CmsSearchBundle\Repository\SearchLogRepository;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(CmsSearchLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class CmsSearchLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getEntityFqcn(): string
    {
        return SearchLog::class;
    }

    public function testIndexPage(): void
    {
        $client = self::createAuthenticatedClient();

        // 使用标准的后台路径
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Navigate to SearchLog CRUD if link exists
        $link = $crawler->filter('a[href*="CmsSearchLogCrudController"]')->first();
        if ($link->count() > 0) {
            $client->click($link->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testSearchLogDataPersistence(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test search logs
        $searchLog1 = new SearchLog();
        $searchLog1->setKeyword('PHP教程');
        $searchLog1->setMemberId(1001);
        $searchLog1->setCategoryId(5);
        $searchLog1->setCount(10);
        $searchLog1->setHit(8);

        $searchLogRepository = self::getService(SearchLogRepository::class);
        self::assertInstanceOf(SearchLogRepository::class, $searchLogRepository);
        $searchLogRepository->save($searchLog1, true);

        $searchLog2 = new SearchLog();
        $searchLog2->setKeyword('Symfony框架');
        $searchLog2->setMemberId(1002);
        $searchLog2->setCategoryId(5);
        $searchLog2->setTopicId(3);
        $searchLog2->setCount(5);
        $searchLog2->setHit(0); // 无命中的搜索记录
        $searchLogRepository->save($searchLog2, true);

        // Verify search logs are saved correctly
        $savedSearchLog1 = $searchLogRepository->findOneBy(['keyword' => 'PHP教程']);
        $this->assertNotNull($savedSearchLog1);
        $this->assertEquals(1001, $savedSearchLog1->getMemberId());
        $this->assertEquals(10, $savedSearchLog1->getCount());
        $this->assertEquals(8, $savedSearchLog1->getHit());

        $savedSearchLog2 = $searchLogRepository->findOneBy(['keyword' => 'Symfony框架']);
        $this->assertNotNull($savedSearchLog2);
        $this->assertEquals(1002, $savedSearchLog2->getMemberId());
        $this->assertEquals(5, $savedSearchLog2->getCount());
        $this->assertEquals(0, $savedSearchLog2->getHit());
    }

    public function testSearchLogToString(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setKeyword('测试关键词');

        // Test empty ID case
        $this->assertEquals('', (string) $searchLog);

        // Test with ID (simulate persisted entity)
        $reflection = new \ReflectionClass($searchLog);
        $parentClass = $reflection->getParentClass();
        if ($parentClass instanceof \ReflectionClass && $parentClass->hasProperty('id')) {
            $idProperty = $parentClass->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($searchLog, 12345);
            $this->assertEquals('测试关键词 (12345)', (string) $searchLog);
        } else {
            // 如果无法设置ID，测试其他逻辑
            $this->assertEquals('', (string) $searchLog);
        }
    }

    public function testRetrieveApiArray(): void
    {
        $searchLog = new SearchLog();
        $searchLog->setKeyword('API测试关键词');
        $searchLog->setCount(15);

        // Simulate persisted entity with ID
        $reflection = new \ReflectionClass($searchLog);
        $parentClass = $reflection->getParentClass();
        if ($parentClass instanceof \ReflectionClass && $parentClass->hasProperty('id')) {
            $idProperty = $parentClass->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($searchLog, 54321);

            $apiArray = $searchLog->retrieveApiArray();

            $expected = [
                'id' => 54321,
                'keyword' => 'API测试关键词',
                'count' => 15,
            ];

            $this->assertEquals($expected, $apiArray);
        } else {
            // 如果无法设置ID，测试基本功能
            $apiArray = $searchLog->retrieveApiArray();
            $this->assertArrayHasKey('keyword', $apiArray);
            $this->assertArrayHasKey('count', $apiArray);
            $this->assertEquals('API测试关键词', $apiArray['keyword']);
            $this->assertEquals(15, $apiArray['count']);
        }
    }

    protected function getControllerService(): CmsSearchLogCrudController
    {
        return self::getService(CmsSearchLogCrudController::class);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        // 基于 SearchLogCrudController::configureFields 中的索引页字段
        yield 'ID' => ['ID'];
        yield '用户ID' => ['用户ID'];
        yield '关键词' => ['关键词'];
        yield '搜索次数' => ['搜索次数'];
        yield '命中数' => ['命中数'];
        yield '创建时间' => ['创建时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // SearchLogCrudController 禁用了 NEW 操作，但为了满足抽象方法要求
        // 提供基于 configureFields 的表单字段（使用属性名称，而非标签）
        yield 'memberId' => ['memberId'];
        yield 'keyword' => ['keyword'];
        yield 'categoryId' => ['categoryId'];
        yield 'topicId' => ['topicId'];
        yield 'count' => ['count'];
        yield 'hit' => ['hit'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // 基于 SearchLogCrudController::configureFields 中非 hideOnForm 的字段
        // 使用实际的属性名称，而非中文标签
        yield 'memberId' => ['memberId'];
        yield 'keyword' => ['keyword'];
        yield 'categoryId' => ['categoryId'];
        yield 'topicId' => ['topicId'];
        yield 'count' => ['count'];
        yield 'hit' => ['hit'];
    }
}
