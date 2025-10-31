<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests\Service;

use EasyCorp\Bundle\EasyAdminBundle\Registry\DashboardControllerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Registry\DashboardControllerRegistryInterface;

/**
 * 测试环境专用Dashboard控制器注册表
 * 确保只注册我们的TestDashboard，避免多Dashboard冲突
 */
final class TestDashboardControllerRegistry implements DashboardControllerRegistryInterface
{
    public function __construct(
        private readonly string $testDashboardFqcn,
    ) {
    }

    public function getControllerFqcnByContextId(string $contextId): ?string
    {
        if ('cms-search-test' === $contextId) {
            return $this->testDashboardFqcn;
        }

        return null;
    }

    public function getContextIdByControllerFqcn(string $controllerFqcn): ?string
    {
        if ($controllerFqcn === $this->testDashboardFqcn) {
            return 'cms-search-test';
        }

        return null;
    }

    public function getControllerFqcnByRoute(string $routeName): ?string
    {
        if ('test_admin' === $routeName) {
            return $this->testDashboardFqcn;
        }

        return null;
    }

    public function getRouteByControllerFqcn(string $controllerFqcn): ?string
    {
        if ($controllerFqcn === $this->testDashboardFqcn) {
            return 'test_admin';
        }

        return null;
    }

    public function getNumberOfDashboards(): int
    {
        return 1;
    }

    public function getFirstDashboardRoute(): string
    {
        return 'test_admin';
    }

    public function getFirstDashboardFqcn(): string
    {
        return $this->testDashboardFqcn;
    }

    /**
     * @return array<int, array{controller: string, route: string, context: string}>
     */
    public function getAll(): array
    {
        return [
            [
                'controller' => $this->testDashboardFqcn,
                'route' => 'test_admin',
                'context' => 'cms-search-test',
            ],
        ];
    }
}
