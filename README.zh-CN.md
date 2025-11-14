# CMS 搜索组件

[English](README.md) | [中文](README.zh-CN.md)

一个为 Symfony 提供内容管理系统（CMS）搜索功能的组件，包含搜索记录日志和管理功能。

## 功能特性

- 搜索日志记录和分析
- 搜索日志管理的后台界面
- 与 EasyAdmin 集成的后端管理
- 支持用户特定和匿名用户搜索追踪
- 按专题和目录进行搜索分类
- 搜索统计和命中数追踪

## 系统要求

- PHP 8.2 或更高版本
- Symfony 7.3 或更高版本
- Doctrine ORM
- EasyAdmin 组件
- tourze/doctrine-snowflake-bundle（用于雪花ID生成）
- tourze/doctrine-timestamp-bundle（用于自动时间戳管理）
- tourze/doctrine-indexed-bundle（用于数据库索引）

## 安装

```bash
composer require tourze/cms-search-bundle
```

## 配置

### 启用组件

```php
// config/bundles.php
return [
    // ...
    Tourze\CmsSearchBundle\CmsSearchBundle::class => ['all' => true],
];
```

### 数据库表结构

组件将自动创建以下数据库表：

```sql
CREATE TABLE ims_cms_search (
    id BIGINT PRIMARY KEY,
    member_id INTEGER NOT NULL DEFAULT 0 COMMENT '用户ID',
    keyword VARCHAR(64) NOT NULL DEFAULT '' COMMENT '关键词',
    category_id INTEGER NOT NULL DEFAULT 0 COMMENT '搜索目录ID',
    topic_id INTEGER NOT NULL DEFAULT 0 COMMENT '搜索专题ID',
    count INTEGER NOT NULL DEFAULT 1 COMMENT '搜索次数',
    hit INTEGER NOT NULL DEFAULT 0 COMMENT '叠加命中数',
    create_time DATETIME NOT NULL,
    update_time DATETIME NOT NULL,

    INDEX idx_member_id (member_id),
    INDEX idx_keyword (keyword),
    INDEX idx_create_time (create_time)
);
```

## 使用方法

### 基础搜索记录

```php
use Tourze\CmsSearchBundle\Entity\SearchLog;
use Doctrine\ORM\EntityManagerInterface;

// 记录搜索
$searchLog = new SearchLog();
$searchLog->setMemberId($userId);
$searchLog->setKeyword('搜索关键词');
$searchLog->setCategoryId($categoryId);
$searchLog->setTopicId($topicId);
$searchLog->setCount(1);
$searchLog->setHit($resultCount);

$entityManager->persist($searchLog);
$entityManager->flush();
```

### 基础仓储操作

```php
use Tourze\CmsSearchBundle\Repository\SearchLogRepository;

/** @var SearchLogRepository $repository */
$repository = $entityManager->getRepository(SearchLog::class);

// 保存搜索记录
$searchLog = new SearchLog();
// ... 设置属性
$repository->save($searchLog);

// 删除搜索记录
$repository->remove($searchLog);

// 自定义查询查找搜索记录
$logs = $repository->findBy(['memberId' => $userId], ['createTime' => 'DESC'], 10);
```

### 字符串表示

```php
// SearchLog 实体实现了 __toString() 方法
echo $searchLog;
// 输出："搜索关键词 (123)"

// 获取 API 友好数组
$data = $searchLog->retrieveApiArray();
// 返回：['id' => 123, 'keyword' => '搜索关键词', 'count' => 5]
```

### 后台管理界面

在 EasyAdmin 中访问搜索日志管理：
- URL：`/admin/cms-search/search-log`
- 功能：查看、筛选和导出搜索日志

## API 参考

### SearchLog 实体

属性：
- `id`：唯一标识符（雪花ID）
- `memberId`：用户ID（0表示匿名用户）
- `keyword`：搜索关键词
- `categoryId`：搜索目录ID（0表示全局搜索）
- `topicId`：搜索专题ID（0表示所有专题）
- `count`：该关键词被搜索的次数
- `hit`：搜索返回的结果数量
- `createTime`：创建时间戳
- `updateTime`：最后更新时间戳

### 方法

```php
// 获取API表示
$data = $searchLog->retrieveApiArray();
// 返回：['id' => 123, 'keyword' => 'term', 'count' => 5]

// 字符串表示
echo (string) $searchLog;
// 返回："搜索关键词 (123)"
```

## 测试

```bash
# 运行测试
composer test

# 运行 PHPStan 分析
composer analyse
```

## 贡献

1. Fork 代码仓库
2. 创建功能分支
3. 提交您的更改
4. 为新功能添加测试
5. 运行测试套件
6. 提交 Pull Request

## 许可证

本组件采用 MIT 许可证。详情请参见 [LICENSE](LICENSE) 文件。

## 更新日志

查看 [CHANGELOG.md](CHANGELOG.md) 了解版本历史和更改列表。
