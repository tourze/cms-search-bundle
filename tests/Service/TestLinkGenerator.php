<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests\Service;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Tourze\CmsSearchBundle\Tests\Controller\Admin\TestDashboardController;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

/**
 * 测试环境专用链接生成器
 * 明确指定使用TestDashboardController来避免多Dashboard冲突
 */
final class TestLinkGenerator implements LinkGeneratorInterface
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public function getCurdListPage(string $entityClass): string
    {
        return $this->adminUrlGenerator
            ->unsetAll()
            ->setDashboard(TestDashboardController::class)
            ->setController($entityClass)
            ->setAction(Action::INDEX)
            ->generateUrl()
        ;
    }

    public function extractEntityFqcn(string $url): ?string
    {
        $params = $this->parseUrlQueryParameters($url);
        if (null === $params) {
            return null;
        }

        if (!isset($params['crudControllerFqcn']) || !is_string($params['crudControllerFqcn'])) {
            return null;
        }

        return $params['crudControllerFqcn'];
    }

    public function setDashboard(string $dashboardControllerFqcn): void
    {
        // 测试环境中无需实际设置 Dashboard，空实现满足接口契约
    }

    /**
     * @return array<int|string, array<mixed>|string>|null
     */
    private function parseUrlQueryParameters(string $url): ?array
    {
        $parsedUrl = parse_url($url);
        // @phpstan-ignore-next-line booleanNot.alwaysFalse (defensive programming for test safety)
        if (false === $parsedUrl || !isset($parsedUrl['query']) || !is_string($parsedUrl['query'])) {
            return null;
        }

        parse_str($parsedUrl['query'], $params);

        return $params;
    }
}
