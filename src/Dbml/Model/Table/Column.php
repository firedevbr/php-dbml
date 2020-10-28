<?php

declare(strict_types=1);

namespace Dbml\Dbml\Model\Table;

use Dbml\Dbml\Model\Table\Type\Enum;

/**
 * Class Column
 * @package Dbml\Dbml\Table
 */
class Column
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var bool
     */
    public $null = true;

    /**
     * @var bool
     */
    public $unsigned = false;

    /**
     * @var int|null
     */
    public $length = null;

    /**
     * @var string|null
     */
    public $default = null;

    /**
     * @var bool
     */
    public $unique = false;

    /**
     * @var bool
     */
    public $autoIncrement = false;

    /**
     * @var Index[]
     */
    public $indexes = [];

    /**
     * @var null|Enum
     */
    public $enum = null;

    /**
     * Column constructor.
     * @param string $name
     * @param string $type
     * @param array $attributes
     * @param Index[]|null $indexes
     */
    public function __construct(
        string $name,
        string $type,
        array $attributes = [],
        array $indexes = [],
        ?Enum $enum = null
    ) {
        $this->name    = $name;
        $this->type    = $type;
        $this->indexes = $indexes;
        $this->initAttributes($attributes);
        $this->enum    = $enum;
    }

    /**
     * @param array $attributes
     * @return Column
     */
    private function initAttributes(array $attributes = []): Column
    {
        foreach ($attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return $this->null;
    }

    /**
     * @param bool $null
     */
    public function setNull(bool $null): void
    {
        $this->null = $null;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * @param bool $unsigned
     */
    public function setUnsigned(bool $unsigned): void
    {
        $this->unsigned = $unsigned;
    }

    /**
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @param int|null $length
     */
    public function setLength(?int $length): void
    {
        $this->length = $length;
    }

    /**
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @param string|null $default
     */
    public function setDefault(?string $default): void
    {
        $this->default = $default;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @param bool $unique
     */
    public function setUnique(bool $unique): void
    {
        $this->unique = $unique;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @param bool $autoIncrement
     */
    public function setAutoIncrement(bool $autoIncrement): void
    {
        $this->autoIncrement = $autoIncrement;
    }

    /**
     * @return Index[]
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @param Index[] $indexes
     */
    public function setIndexes(array $indexes): void
    {
        $this->indexes = $indexes;
    }

    /**
     * @param Index $index
     * @return $this
     */
    public function addIndex(Index $index): self
    {
        $this->indexes[] = $index;

        return $this;
    }

    /**
     * @return Enum|null
     */
    public function getEnum(): ?Enum
    {
        return $this->enum;
    }

    /**
     * @param Enum|null $enum
     */
    public function setEnum(?Enum $enum): void
    {
        $this->enum = $enum;
    }

    /**
     * @return array
     */
    public function getEnumValues(): array
    {
        return $this->enum->getValues();
    }
}