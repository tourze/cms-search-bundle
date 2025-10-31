<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Tourze\CmsSearchBundle\Entity\SearchLog;

/**
 * @extends AbstractCrudController<SearchLog>
 */
#[AdminCrud(
    routePath: '/cms-search/search-log',
    routeName: 'cms_search_search_log'
)]
final class CmsSearchLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SearchLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('搜索记录')
            ->setEntityLabelInPlural('搜索记录管理')
            ->setPageTitle(Crud::PAGE_INDEX, '搜索记录列表')
            ->setPageTitle(Crud::PAGE_NEW, '创建搜索记录')
            ->setPageTitle(Crud::PAGE_EDIT, '编辑搜索记录')
            ->setPageTitle(Crud::PAGE_DETAIL, '搜索记录详情')
            ->setDefaultSort(['createTime' => 'DESC'])
            ->setSearchFields(['keyword'])
            ->showEntityActionsInlined()
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex()
        ;

        yield IntegerField::new('memberId', '用户ID')
            ->setHelp('执行搜索的用户ID，0表示匿名用户')
        ;

        yield TextField::new('keyword', '关键词')
            ->setHelp('用户搜索的关键词')
        ;

        yield IntegerField::new('categoryId', '搜索目录ID')
            ->setHelp('搜索范围限定的目录ID，0表示全站搜索')
            ->hideOnIndex()
        ;

        yield IntegerField::new('topicId', '搜索专题ID')
            ->setHelp('搜索范围限定的专题ID，0表示不限专题')
            ->hideOnIndex()
        ;

        yield IntegerField::new('count', '搜索次数')
            ->setHelp('该关键词被搜索的累计次数')
        ;

        yield IntegerField::new('hit', '命中数')
            ->setHelp('搜索返回的结果数量累计')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->onlyOnDetail()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('keyword', '关键词'))
            ->add(NumericFilter::new('memberId', '用户ID'))
            ->add(NumericFilter::new('categoryId', '目录ID'))
            ->add(NumericFilter::new('topicId', '专题ID'))
            ->add(NumericFilter::new('count', '搜索次数'))
            ->add(NumericFilter::new('hit', '命中数'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
