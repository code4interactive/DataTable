<?php
namespace Code4\DataTable\Test;

use Code4\DataTable\Decorator;

class DecoratorClass extends Decorator {

    protected $columns = ['id', 'name'];

    protected $cellDecorators = [
        'name' => ['cell']
    ];

    protected $rowDecorators = ['row'];

    public function rowDecorator($row) {
        $row['id'] = $row['id'].'Decorated';
        return $row;
    }

    public function cellDecorator($cell, $row)  {
        return $cell.'Decorated';
    }


}