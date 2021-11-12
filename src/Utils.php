<?php
namespace Nio\QueryBuilder;


abstract class Utils
{
    protected function semiJoin($field1, $field2, $anti = false, bool $reverseTables = false)
    {
        $anti = $anti ? "NOT" : "";
        // first table
        $table_a = $this->table;
        $fields_from_a = $this->fields;

        // second table
        $table_b = $this->join['table'];
        $fields_from_b = $this->join['results'] ?? null;
        // fields
        $fields = "{$fields_from_a} , {$fields_from_b}";
        
        if ($reverseTables) {
            list($table_a, $table_b) = [$table_b, $table_a];
        }

        $sql = "SELECT {$fields} FROM {$table_a} WHERE {$field1} {$anti} IN ( ";
        $sql .= "SELECT {$field2} FROM {$table_b}) ;";

        return $sql;
    }


    protected function modifiers(&$sql)
    {
        // sql select statment elements order
        $sql_order = ['GROUP BY', 'HAVING', 'ORDER BY', 'LIMIT', 'OFFSET'];
        // put if isset , but in order 
        foreach ($sql_order as $value) {
            if (isset($this->vars[$value])) {
                $sql .= " {$value} {$this->vars[$value]} ";
            }
        }
    }
}
