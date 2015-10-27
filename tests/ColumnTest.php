<?php
namespace Code4\DataTable\Test;

use Code4\DataTable\Column;
use Mockery as m;

class ColumnTest extends \PHPUnit_Framework_TestCase
{

    public function testColumnAttributes()
    {
        $htmlBuilder = m::mock('Collective\Html\HtmlBuilder');
        $column = new Column('colId', ['name'=>'Kolumna'], $htmlBuilder);

        $this->assertEquals('colId', $column->getId());
        $this->assertEquals('Kolumna', $column->name);
        $this->assertEquals(true, $column->orderable);
        $this->assertEquals(true, $column->searchable);

        $this->assertEquals(false, isset($column->content));
        $this->assertEquals(null, $column->content);

        $this->assertEquals(true, $column->isSortable());
    }

}
