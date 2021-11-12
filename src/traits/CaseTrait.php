<?php

namespace Nio\QueryBuilder\traits;

use Exception;

trait CaseTrait
{

    public function when(string $str, array $params = [])
    {
        $this->map['WHEN'][] = [$str];
        $this->map['PARAMS'] = array_merge($this->map['PARAMS'] ?? [],  $params);
        return $this;
    }

    public function then($value)
    {
        $this->map['WHEN'][count($this->map['WHEN']) - 1]['then'] = $value;
        return $this;
    }

    public function for($value)
    {
        $this->map['WHEN'][count($this->map['WHEN']) - 1]['for'] = $value;
        return $this;
    }

    public function then_else($value)
    {
        $this->map['WHEN'][count($this->map['WHEN']) - 1]['else'] = $value;
        return $this;
    }

    public function caseCond()
    {
        if (isset($this->map['WHEN'])) {
            $s = ' CASE ';

            foreach ($this->map['WHEN'] as $value) {
                $str = $value[0];
                $for = $value['for'] ?? null;
                $else = $value['else'] ?? null;
                $then = $value['then'] ?? null;

                if (is_null($then)) {
                    throw new Exception("A Then value must be given");
                }

                $s .= " WHEN {$str} THEN {$then} ";
                if ($else) $s .= " ELSE {$else} ";
                $s .= " FOR {$for} , ";
            }

            $s = substr($s, 0, strlen($s) - 2);
        }

        return trim($s);
    }
}
