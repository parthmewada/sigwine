<?php
class MultiDimensionSort
{
    const ASCENDING = 0,DESCENDING = 1;

    public  $sortColumn,$sortType;

    public function __construct($column = 'price', $type = self::ASCENDING)
    {
        $this->column = $column;
        $this->type = $type;
    }

    public function cmp($a, $b)
    {
        switch($this->type)
        {
            case self::ASCENDING:
                return ($a[$this->column] == $b[$this->column]) ? 0 : (($a[$this->column] < $b[$this->column]) ? -1 : 1);
            case self::DESCENDING:
                return ($a[$this->column] == $b[$this->column]) ? 0 :-(($a[$this->column] < $b[$this->column]) ? 1 : -1);
            default:
                assert(0); // unkown type
        }
    }
}
?>