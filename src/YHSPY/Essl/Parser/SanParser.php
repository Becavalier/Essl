<?php

namespace YHSPY\Essl\Parser;

use YHSPY\Essl\Exception;

class SanParser
{
    /**
     * @param $sanString
     * @return array
     */
    public function parse($sanString)
    {
        $results = explode(',', $sanString);

        array_walk($results, function(&$item) {
            $item = trim($item);
            $item = str_replace("DNS:", "", $item);
            $item = strtolower($item);
        });

        return $results;
    }
}