<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\CmsSearchBundle\Entity\SearchLog;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(SearchLog::class)]
final class SearchLogTest extends AbstractEntityTestCase
{
    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'memberId' => ['memberId', 123];
        yield 'keyword' => ['keyword', 'test keyword'];
        yield 'categoryId' => ['categoryId', 1];
        yield 'topicId' => ['topicId', 3];
        yield 'count' => ['count', 5];
        yield 'hit' => ['hit', 0];
    }

    protected function createEntity(): SearchLog
    {
        return new SearchLog();
    }

    public function testStringable(): void
    {
        $searchLog = $this->createEntity();

        // 测试空ID时返回空字符串
        $result = $searchLog->__toString();
        self::assertSame('', $result);

        // 测试有关键词时的字符串表示（ID为null时返回空字符串）
        $searchLog->setKeyword('test keyword');
        $result = $searchLog->__toString();
        self::assertSame('', $result);  // ID为null时返回空字符串

        // 测试有ID时的字符串表示（需要通过 reflection 设置 ID）
        $reflection = new \ReflectionClass(SearchLog::class);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($searchLog, 123);

        $result = $searchLog->__toString();
        self::assertSame('test keyword (123)', $result);
    }

    public function testInitialValues(): void
    {
        $searchLog = $this->createEntity();

        self::assertNull($searchLog->getId());
        self::assertSame(0, $searchLog->getCategoryId());
        self::assertSame(0, $searchLog->getTopicId());
        self::assertSame(1, $searchLog->getCount());
        self::assertSame(0, $searchLog->getHit());
    }

    public function testRetrieveApiArray(): void
    {
        $keyword = 'test keyword';
        $count = 10;
        $searchLog = $this->createEntity();

        $searchLog->setKeyword($keyword);
        $searchLog->setCount($count);

        $apiArray = $searchLog->retrieveApiArray();

        self::assertArrayHasKey('id', $apiArray);
        self::assertArrayHasKey('keyword', $apiArray);
        self::assertArrayHasKey('count', $apiArray);
        self::assertSame($keyword, $apiArray['keyword']);
        self::assertSame($count, $apiArray['count']);
    }

    public function testDefaultValues(): void
    {
        $searchLog = $this->createEntity();

        // 测试默认值
        self::assertSame(0, $searchLog->getCategoryId());
        self::assertSame(0, $searchLog->getTopicId());
        self::assertSame(1, $searchLog->getCount());
        self::assertSame(0, $searchLog->getHit());
    }

    public function testZeroValues(): void
    {
        $searchLog = $this->createEntity();

        // 测试设置为0的值
        $searchLog->setMemberId(0);
        $searchLog->setCategoryId(0);
        $searchLog->setTopicId(0);
        $searchLog->setCount(0);
        $searchLog->setHit(0);

        self::assertSame(0, $searchLog->getMemberId());
        self::assertSame(0, $searchLog->getCategoryId());
        self::assertSame(0, $searchLog->getTopicId());
        self::assertSame(0, $searchLog->getCount());
        self::assertSame(0, $searchLog->getHit());
    }
}
