<?php

declare(strict_types=1);

namespace Tourze\CmsSearchBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\CmsSearchBundle\Repository\SearchLogRepository;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * 搜索记录表.
 */
#[ORM\Table(name: 'ims_cms_search', options: ['comment' => '搜索记录表'])]
#[ORM\Entity(repositoryClass: SearchLogRepository::class)]
class SearchLog implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '用户ID'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private int $memberId = 0;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 64, nullable: false, options: ['default' => '', 'comment' => '关键词'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $keyword = '';

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '搜索目录ID'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private int $categoryId = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '搜索专题ID'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private int $topicId = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 1, 'comment' => '搜索次数'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private int $count = 1;

    #[ORM\Column(type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '叠加命中数'])]
    #[Assert\Type(type: 'int')]
    #[Assert\PositiveOrZero]
    private int $hit = 0;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return $this->getKeyword() . ' (' . $this->getId() . ')';
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'keyword' => $this->getKeyword(),
            'count' => $this->getCount(),
        ];
    }

    public function getMemberId(): int
    {
        return $this->memberId;
    }

    public function setMemberId(int $memberId): void
    {
        $this->memberId = $memberId;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getTopicId(): int
    {
        return $this->topicId;
    }

    public function setTopicId(int $topicId): void
    {
        $this->topicId = $topicId;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function getHit(): int
    {
        return $this->hit;
    }

    public function setHit(int $hit): void
    {
        $this->hit = $hit;
    }
}
