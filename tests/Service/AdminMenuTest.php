<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\CmsSearchBundle\Service\AdminMenu;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 初始化测试环境
    }

    public function testInvokeAddsSearchManagementToContentCenter(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $menuItem = $this->createMock(ItemInterface::class);
        $contentCenterItem = $this->createMock(ItemInterface::class);

        // 模拟内容中心不存在的情况
        $menuItem
            ->expects($this->exactly(2))
            ->method('getChild')
            ->with('内容中心')
            ->willReturnOnConsecutiveCalls(null, $contentCenterItem)
        ;

        $menuItem
            ->expects($this->once())
            ->method('addChild')
            ->with('内容中心')
            ->willReturn($contentCenterItem)
        ;

        $contentCenterItem
            ->expects($this->once())
            ->method('setExtra')
            ->with('permission', 'CmsBundle')
        ;

        $searchMenuItem = $this->createMock(ItemInterface::class);

        $contentCenterItem
            ->expects($this->once())
            ->method('addChild')
            ->with('搜索管理')
            ->willReturn($searchMenuItem)
        ;

        $searchMenuItem
            ->expects($this->once())
            ->method('setUri')
            ->with(self::stringContains('SearchLog'))
        ;

        ($adminMenu)($menuItem);
    }

    public function testInvokeUsesExistingContentCenter(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $menuItem = $this->createMock(ItemInterface::class);
        $contentCenterItem = $this->createMock(ItemInterface::class);

        // 模拟内容中心已存在的情况
        $menuItem
            ->expects($this->exactly(2))
            ->method('getChild')
            ->with('内容中心')
            ->willReturn($contentCenterItem)
        ;

        $menuItem
            ->expects($this->never())
            ->method('addChild')
        ;

        $searchMenuItem = $this->createMock(ItemInterface::class);

        $contentCenterItem
            ->expects($this->once())
            ->method('addChild')
            ->with('搜索管理')
            ->willReturn($searchMenuItem)
        ;

        $searchMenuItem
            ->expects($this->once())
            ->method('setUri')
            ->with(self::stringContains('SearchLog'))
        ;

        ($adminMenu)($menuItem);
    }

    public function testInvokeHandlesNullContentCenter(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $menuItem = $this->createMock(ItemInterface::class);
        $contentCenterItem = $this->createMock(ItemInterface::class);

        // 模拟创建内容中心后为null的情况
        $menuItem
            ->expects($this->exactly(2))
            ->method('getChild')
            ->with('内容中心')
            ->willReturnOnConsecutiveCalls(null, null)
        ;

        $menuItem
            ->expects($this->once())
            ->method('addChild')
            ->with('内容中心')
            ->willReturn($contentCenterItem)
        ;

        $contentCenterItem
            ->expects($this->once())
            ->method('setExtra')
            ->with('permission', 'CmsBundle')
        ;

        ($adminMenu)($menuItem);
    }
}
