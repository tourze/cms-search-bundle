<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\CmsSearchBundle\CmsSearchBundle;
use Tourze\CmsSearchBundle\Tests\Controller\Admin\TestDashboardController;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

/**
 * 测试专用的内核
 */
final class CmsSearchKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SecurityBundle();
        yield new DoctrineIndexedBundle();
        yield new DoctrineSnowflakeBundle();
        yield new DoctrineTimestampBundle();
        yield new EasyAdminMenuBundle();
        yield new CmsSearchBundle();
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // 只加载我们自己的测试路由
        $routes->import(__DIR__ . '/Controller/Admin/TestDashboardController.php', 'attribute');

        // 加载EasyAdmin路由，但限制到我们的测试控制器
        $routes->add('cms_search_test_admin', '/test-admin')
            ->controller(['tourze.cms_search.test_dashboard_controller', 'index'])
        ;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        // $container->import('../config/services.yaml');

        // Framework配置
        $container->extension('framework', [
            'test' => true,
            'router' => [
                'utf8' => true,
            ],
            'secret' => 'test',
            'http_method_override' => false,
            'trusted_hosts' => ['test'],
            'trusted_proxies' => [],
            'trusted_headers' => [
                'x-forwarded-for',
                'x-forwarded-host',
                'x-forwarded-proto',
                'x-forwarded-port',
                'x-forwarded-prefix',
            ],
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
        ]);

        // Security配置
        $container->extension('security', [
            'enable_authenticator_manager' => true,
            'password_hashers' => [
                'Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface' => 'plaintext',
            ],
            'providers' => [
                'test_users' => [
                    'memory' => [
                        'users' => [
                            'admin' => [
                                'password' => 'admin',
                                'roles' => ['ROLE_ADMIN'],
                            ],
                        ],
                    ],
                ],
            ],
            'firewalls' => [
                'test_admin' => [
                    'lazy' => true,
                    'provider' => 'test_users',
                    'pattern' => '^/test-admin',
                    'form_login' => [
                        'login_path' => '/test-admin/login',
                        'check_path' => '/test-admin/login_check',
                    ],
                    'logout' => [
                        'path' => '/test-admin/logout',
                    ],
                ],
            ],
            'access_control' => [
                [
                    'path' => '^/test-admin/login',
                    'roles' => 'PUBLIC_ACCESS',
                ],
                [
                    'path' => '^/test-admin',
                    'roles' => 'ROLE_ADMIN',
                ],
            ],
        ]);

        $services = $container->services();
        $this->configureTestServices($services);
    }

    private function configureTestServices(ServicesConfigurator $services): void
    {
        // 注册测试用的Dashboard控制器
        $services->set('tourze.cms_search.test_dashboard_controller', TestDashboardController::class)
            ->tag('controller.service_arguments')
            ->args([
                '$container' => '@service_container',
            ])
        ;
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/cms_search_test_' . md5($this->environment);
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/cms_search_test_logs_' . md5($this->environment);
    }
}
