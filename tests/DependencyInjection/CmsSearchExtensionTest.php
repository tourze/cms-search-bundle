<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\CmsSearchBundle\DependencyInjection\CmsSearchExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

/**
 * @internal
 */
#[CoversClass(CmsSearchExtension::class)]
final class CmsSearchExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testExtensionInstance(): void
    {
        $extension = new CmsSearchExtension();

        // 通过反射验证继承关系
        self::assertContains(AutoExtension::class, class_parents($extension));
        self::assertEquals('cms_search', $extension->getAlias());
    }
}
