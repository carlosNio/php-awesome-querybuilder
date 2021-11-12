<?php

namespace Nio\QueryBuilder\traits;

use Exception;

trait JoinTrait
{

    public function fields(array $fields)
    {
        // more than one
        if (is_array($fields[0])) {
            $f = [];

            // iterate all and insert her respective table
            foreach ($fields as $index => $array) {
                $f = array_merge($f, $this->identifyTable($index, $array));
            }

            $fields = $f;
            // 
        } else {
            // get the first
            $table = $this->getTable();

            // add the table to the field
            $fields = array_map(function ($item) use ($table) {
                if (!preg_match('/^\w+\(/', $item))
                    return "{$table}.{$item}";
                else
                    return $item;
            }, $fields);
            // 
        }

        $this->fields = implode(" , ", $fields);
    }


    public function join(string $table_b, array $fields = null)
    {

        if ($fields) {
            $fields = $this->identifyTable($table_b, $fields);
            $this->join[$table_b] = implode(" , ", $fields);
        }

        $this->join['table'] = $table_b;
        return $this;
    }

    public function on(string $f1, string $f2)
    {
        $this->join['on'] = "{$f1} = {$f2}";
        return $this;
    }


    private function buildJoin()
    {
        $on = $this->join['on'] ?? null;
        $table_b = $this->join['table'] ?? '';
        $table_b_fields = $this->join[$table_b] ?? '';

        if (is_null($on)) {
            throw new Exception("Where condiction inside JOIN statment must be given");
        }

        if (empty($table_b)) {
            throw new Exception("No Table to join");
        }

        $table_a = $this->getTable();
        $table_a_fields = $this->fields;
        $sql = '';

        if ($a = $table_a_fields) $sql .= $table_a_fields;

        if ($table_b_fields) {
            if ($a) $sql .= " , ";
            $sql .= $table_b_fields;
        }

        $sql .= " FROM {$table_a} JOIN {$table_b} ON {$on} ";

        return $sql;
    }

    private function getTable(int $index = 0)
    {

        if (strpos(",", $this->table) === false) {
            $array = explode(" , ", $this->table);

            if ($index > (count($array) - 1)) {
                throw new Exception("Invalid table");
            }

            $table = $array[$index];
            // 
        } else $table = $this->table;

        // remove table aliases for fields
        if (str_word_count($table) == 2) {
            $table = substr($table, 0, strpos($table, " "));
        }

        return $table;
    }


    private function identifyTable($table, array $fields)
    {
        if (!strpos(",", $this->table) === false) {
            throw new Exception("Only one table given");
        }
        // if the table is a numeric index
        if (is_int($table)) {
            // stored tables
            $tables = explode(" , ", $this->table);
            // invalid index position
            if ($table > (count($tables) - 1)) {
                throw new Exception("Invalid table");
            }

            $table = $tables[$table];
        }
        // remove table aliases for fields
        if (str_word_count($table) == 2) {
            $table = substr($table, 0, strpos($table, " "));
        }
        // if has just one element and that is the * return it
        if (count($fields) == 1 and $fields[0] == "*") {
            return ["{$table}.*"];
        }
        // identify the table and the field
        $specified = array_map(function ($item) use ($table) {
            return "{$table}.{$item}";
        }, $fields);

        return $specified;
    }
}
