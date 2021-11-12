<?php
namespace Nio\QueryBuilder;

use Exception;

/**
 * Build INSERT Statments
 * 
 * @author Carlos Bumba git:@CarlosNio
 */
class Insert
{
    private $table;
    private $data;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function add(array $data)
    {
        $this->data = $data;
    }

    public function results()
    {
        if (!isset($this->data))
            throw new Exception("No data to insert");

        $data = $this->data;
        // fields to insert
        $fields = implode(" , ", array_keys($data));
        //    placehlders for the parameters
        $placesholders = array_fill(0, count($data), "?");
        $placesholders = implode(" , ", $placesholders);
        //   values to insert
        $data = array_values($data);
        // insert statment
        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placesholders}) ;";

        return [$sql, $data];
    }
}
