<?php
namespace Code4\DataTable\Test;

class MenuCollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testCellAndRowDecorations()
    {
        $decorator = new DecoratorClass();
        $data = $decorator->decorate([['id'=>0, 'name'=>'test'], ['id'=>1, 'name'=>'test']]);

        $this->assertEquals('0Decorated', $data[0]['id']);
        $this->assertEquals('testDecorated', $data[0]['name']);
    }

}
