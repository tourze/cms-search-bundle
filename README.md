# CMS Search Bundle

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle that provides CMS search functionality with search logging and management features.

## Features

- Search logging and analytics
- Admin interface for managing search logs
- Integration with EasyAdmin for backend management
- Support for user-specific and anonymous search tracking
- Search categorization by topic and category
- Search statistics and hit tracking

## Requirements

- PHP 8.2 or higher
- Symfony 7.3 or higher
- Doctrine ORM
- EasyAdmin Bundle
- tourze/doctrine-snowflake-bundle (for Snowflake ID generation)
- tourze/doctrine-timestamp-bundle (for automatic timestamp management)
- tourze/doctrine-indexed-bundle (for database indexing)

## Installation

```bash
composer require tourze/cms-search-bundle
```

## Configuration

### Enable the Bundle

```php
// config/bundles.php
return [
    // ...
    Tourze\CmsSearchBundle\CmsSearchBundle::class => ['all' => true],
];
```

### Database Schema

The bundle will create the following table automatically:

```sql
CREATE TABLE ims_cms_search (
    id BIGINT PRIMARY KEY,
    member_id INTEGER NOT NULL DEFAULT 0,
    keyword VARCHAR(64) NOT NULL DEFAULT '',
    category_id INTEGER NOT NULL DEFAULT 0,
    topic_id INTEGER NOT NULL DEFAULT 0,
    count INTEGER NOT NULL DEFAULT 1,
    hit INTEGER NOT NULL DEFAULT 0,
    create_time DATETIME NOT NULL,
    update_time DATETIME NOT NULL,

    INDEX idx_member_id (member_id),
    INDEX idx_keyword (keyword),
    INDEX idx_create_time (create_time)
);
```

## Usage

### Basic Search Logging

```php
use Tourze\CmsSearchBundle\Entity\SearchLog;
use Doctrine\ORM\EntityManagerInterface;

// Log a search
$searchLog = new SearchLog();
$searchLog->setMemberId($userId);
$searchLog->setKeyword('search term');
$searchLog->setCategoryId($categoryId);
$searchLog->setTopicId($topicId);
$searchLog->setCount(1);
$searchLog->setHit($resultCount);

$entityManager->persist($searchLog);
$entityManager->flush();
```

### Basic Repository Operations

```php
use Tourze\CmsSearchBundle\Repository\SearchLogRepository;

/** @var SearchLogRepository $repository */
$repository = $entityManager->getRepository(SearchLog::class);

// Save a search log
$searchLog = new SearchLog();
// ... set properties
$repository->save($searchLog);

// Remove a search log
$repository->remove($searchLog);

// Find search logs with custom queries
$logs = $repository->findBy(['memberId' => $userId], ['createTime' => 'DESC'], 10);
```

### String Representation

```php
// The SearchLog entity implements __toString()
echo $searchLog;
// Output: "search keyword (123)"

// Get API-friendly array
$data = $searchLog->retrieveApiArray();
// Returns: ['id' => 123, 'keyword' => 'search keyword', 'count' => 5]
```

### Admin Interface

Access the search log management in EasyAdmin at:
- URL: `/admin/cms-search/search-log`
- Features: View, filter, and export search logs

## API Reference

### SearchLog Entity

Properties:
- `id`: Unique identifier (Snowflake ID)
- `memberId`: User ID (0 for anonymous users)
- `keyword`: Search keyword
- `categoryId`: Search category ID (0 for global search)
- `topicId`: Search topic ID (0 for all topics)
- `count`: Number of times this keyword was searched
- `hit`: Number of search results found
- `createTime`: Creation timestamp
- `updateTime`: Last update timestamp

### Methods

```php
// Get API representation
$data = $searchLog->retrieveApiArray();
// Returns: ['id' => 123, 'keyword' => 'term', 'count' => 5]

// String representation
echo (string) $searchLog;
// Returns: "search term (123)"
```

## Testing

```bash
# Run tests
composer test

# Run PHPStan analysis
composer analyse
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Run the test suite
6. Submit a pull request

## License

This bundle is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.