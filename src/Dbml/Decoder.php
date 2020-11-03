<?php

declare(strict_types=1);

namespace Dbml\Dbml;

use Dbml\Dbml\Model\Table;
use Exception;

/**
 * Class Decoder
 * @package Dbml\Dbml
 */
class Decoder
{
    /**
     * @param string $content
     * @return Table[]
     * @throws Exception
     */
    public static function run(string $content): array
    {
        return self::decodeTables($content);
    }

    /**
     * @param string $content
     * @return array
     * @throws Exception
     */
    private static function decodeTables(string $content): array
    {
        $result = [];

        $re = '/Table (?|([\w]+)|([\w]+) as ([\w]+)) (?|\{\s*([^}]*)\}[\W]+}|\{\s*([^}]*)})/m';
        preg_match_all($re, $content, $tables, PREG_SET_ORDER);

        $reEnums = '/enum ([\w_]+)(\s*\{([^}]*)})/m';
        preg_match_all($reEnums, $content, $enums, PREG_SET_ORDER);

        $enums = self::decodeEnums($enums);

        foreach ($tables as $item) {
            $indexesDefinitions = self::getIndexesDefinitions(trim($item[0]));

            $name = trim($item[1]);
            $alias = !empty($item[2]) ? trim($item[2]) : null;
            $item[3] = preg_replace('/indexes \{\s*([^}]*)/m', '', $item[3]);
            $columns = self::decodeColumns(trim($item[3]), $enums);
            $indexes = [];
            if (!empty($indexesDefinitions)) {
                $indexes = self::decodeIndexes($indexesDefinitions[0][1], $columns);
            }
            $result[] = new Model\Table($name, $alias, $columns, [], $indexes);
        }

        // -- relationships
        self::decodeRelationships($content, $result);

        return $result;
    }

    /**
     * @param string $content
     * @return Table\Column[]
     */
    private static function decodeColumns(string $content, array $enums = []): array
    {
        $result = [];

        $enumsIds = array_keys($enums);

        $re = '/([\w_]+) ([a-z\(\w\)]+)( \[(.*)])?/m';
        preg_match_all($re, $content, $columns, PREG_SET_ORDER);

        foreach ($columns as $item) {
            $name = trim($item[1]);
            $type = trim($item[2]);
            $enum = null;
            if (in_array($type, $enumsIds)) {
                /** @var Table\Type\Enum $selectedEnum */
                $enum = $enums[$type];
                $type = 'enum';
            }

            $attributes = [];
            if (!empty($item[4])) {
                $attributes = self::decodeAttributes(trim($item[4]));
            }

            $result[] = new Table\Column($name, $type, $attributes, [], $enum);
        }

        return $result;
    }

    /**
     * @param string $indexesDefinitions
     * @return Table\Index[]
     */
    private static function decodeIndexes(string $indexesDefinitions, array $tableColumns = []): array
    {
        $result = [];
        $re = '/(?|(\([\w\s,]*\))|([\w]+))( \[(.*)])?/m';
        preg_match_all($re, $indexesDefinitions, $indexes, PREG_SET_ORDER);

        foreach ($indexes as $index) {
            $associatedColumns  = preg_replace('(\(|\))', '', $index[1]);
            $associatedColumns  = explode(", ", $associatedColumns);
            $selectedColumns    = [];

            /** @var Table\Column $tableColumn */
            foreach ($tableColumns as $tableColumn) {
                if (in_array($tableColumn->getName(), $associatedColumns)) {
                    $selectedColumns[] = $tableColumn;
                }
            }

            $indexObject = new Table\Index($selectedColumns);
            if (isset($index[3])) {
                $settings = explode(", ", $index[3]);

                if (in_array('pk', $settings)) {
                    $indexObject->setPrimaryKey(true);
                }
                if (in_array('unique', $settings)) {
                    $indexObject->setUnique(true);
                }
            }

            $result[] = $indexObject;
        }

        return $result;
    }

    /**
     * @param string $content
     * @return array
     */
    public static function decodeAttributes(string $content): array
    {
        $result = [];

        $attributes = explode(',', $content);
        foreach ($attributes as $attribute) {
            $attribute = trim($attribute);
            if ($attribute === 'pk' || $attribute === 'primary key') {
                $result['null'] = false;
            }
            if ($attribute === 'not null') {
                $result['null'] = false;
            }
            if ($attribute === 'unique') {
                $result['unique'] = true;
            }
            if ($attribute === 'increment') {
                $result['autoIncrement'] = true;
            }
        }

        return $result;
    }

    /**
     * @param string $content
     * @param Table[] $tables
     * @return Table[]
     * @throws Exception
     */
    public static function decodeRelationships(string $content, array $tables): array
    {
        $result = [];

        $re = '/Ref: \"?([^".\s]+)\"?\.\"?([^".\s]+)\"? ([<>-]{1}) \"?([^".\s]+)\"?\.\"?([^".\s]+)\"?/m';
        preg_match_all($re, $content, $relationships, PREG_SET_ORDER);

        foreach ($relationships as $item) {

            $type = self::decodeRelationshipType($item[3]);

            // -- get relationship table
            $table = null;
            foreach ($tables as $table) {
                if ($item[1] === $table->name || $item[1] === $table->alias) {
                    break;
                }
            }
            // -- get column
            $column = null;
            foreach ($table->columns as $column) {
                if ($item[2] === $column->name) {
                    break;
                }
            }
            // -- get foreign table
            $foreignTable = null;
            foreach ($tables as $foreignTable) {
                if ($item[4] === $foreignTable->name || $item[4] === $foreignTable->alias) {
                    break;
                }
            }
            // -- get foreign column
            $foreignColumn = null;
            foreach ($foreignTable->columns as $foreignColumn) {
                if ($item[5] === $foreignColumn->name) {
                    break;
                }
            }

            $relationship = new Table\Relationship($type, $column, $foreignTable, $foreignColumn);
            $table->addRelationship($relationship);
        }

        return $tables;
    }

    /**
     * @param string $type
     * @return string
     * @throws Exception
     */
    private static function decodeRelationshipType(string $type): string
    {
        switch ($type) {
            case '<':
                return Table\Relationship::RELATIONSHIP_HAS_MANY;
                break;
            case '>':
                return Table\Relationship::RELATIONSHIP_BELONGS_TO;
                break;
            case '-':
                return Table\Relationship::RELATIONSHIP_HAS_ONE;
                break;
        }

        throw new Exception('Unsupported type.');
    }

    private static function getIndexesDefinitions(string $tableDefinition): array
    {
        $re = '/indexes \{\s*([^}]*)}/m';
        preg_match_all($re, $tableDefinition, $indexes, PREG_SET_ORDER);

        return $indexes;
    }

    /**
     * @param array $enums
     */
    private static function decodeEnums(array $enums): array
    {
        $result = [];

        foreach ($enums as $enum) {
            $identifier = $enum[1];
            $values     = explode("\n", trim($enum[3]));

            $values = array_map(function ($value) {
                return trim(str_replace("\"", "", $value));
            }, $values);

            $enumObj = new Table\Type\Enum($identifier, $values);
            $result[$identifier] = $enumObj;
        }

        return $result;
    }
}