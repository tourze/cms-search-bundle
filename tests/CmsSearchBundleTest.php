<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\BundleDependency\BundleDependencyInterface;

/**
 * @internal
 */
#[CoversClass(CmsSearchBundle::class)]
final class CmsSearchBundleTest extends TestCase
{
    public function testBundleImplementsDependencyInterface(): void
    {
        $bundle = new CmsSearchBundle();

        // 通过反射验证接口实现
        self::assertContains(BundleDependencyInterface::class, class_implements($bundle));
    }
}
