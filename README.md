# DataTable - Laravel Package

[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Circle CI][ico-circle]](https://circleci.com/gh/code4interactive/DataTable/tree/L4)

Simple jQuery DataTable plugin integration

## Install

Via Composer

``` json
//composer.json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/code4interactive/DataTable"
    }
],
"require": {
    "code4interactive/DataTable": "*"
}

composer install / update
```

## Service provider
Code4\DataTable\DataTableServiceProvider

## Usage

Make class which implements DataTable abstract class:

``` php

use Code4\DataTable\DataTable;

class MyDataTableClass extends DataTable {

    protected $name = "MyDataTable";

    protected $columns = [
        'id'          => 'Id',
        'name'        => ['title' => 'Name', 'sort' => 'asc'],
        'type'        => ['title' => 'Typ'],
        'short_name'  => ['title' => 'Skrócona nazwa'],
        'description' => ['title' => 'Opis', 'defaultContent' => '...'],
        'actions'     => ['title' => 'Akcje', 'orderable' => false, 'searchable' => false, 'width' => '100px']
    ];

    protected $cellDecorators = [
        'actions' => ['buttons'],
        'type'    => ['type']
    ];

    protected $url = '/url/to/data/source';

    public function beforeRender() {
        $this->column('id')->addCheckbox();
    }

    protected function getData($start, $length, $search, $orderCol, $orderDir) {
        $company = new Models\CompanyAssets();
        return $company->getDataForDataTable($start, $length, $search, $orderCol, $orderDir);
    }

    protected function countAll() {
        return Models\CompanyAssets::count();
    }

    protected function typeDecorator($cell, $row) {
        return '<strong>'.$cell.'</strong>';
    }

    protected function buttonsDecorator($cell, $row)
    {
        return '<div class="pull-right">' .
        '<a href="/erp/assets/' . $row['id'] . '/generateBadge" class="btn btn-xs btn-info generateQr loadInModal" data-modalId="qrBadge"><i class="fa fa-qrcode"></i></a>&nbsp;' .
        '<a href="/erp/assets/' . $row['id'] . '/edit" class="btn btn-xs btn-info editModal" data-modalId="editAsset"><i class="fa fa-pencil"></i></a>&nbsp;' .
        '<a href="/erp/assets/' . $row['id'] . '/delete" class="btn btn-xs btn-danger confirmDelete" data-name="' . $row['name'] . '"><i class="fa fa-trash"></i></a>' .
        '</div>';
    }

    public function afterDrawCallBack() {

    }
}

```

Make instance of created class and pass object to view to render table and scripts
``` php
//Controller
$dt = DataTable::make(MyDataTableClass::class);
return view('myview', compact('dt'));

//View
{!! $dt->renderTable() !!}
{!! $dt->renderScript() !!}

//Controller method for rendering data
public function renderData(Request $request) {
    return DataTable::make(MyDataTableClass::class)->renderData($request);
}
```



## Testing

``` bash
composer test
```

## Credits

- [Artur Bartczak][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/code4interactive/DataTable.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/code4interactive/DataTable.svg?style=flat-square
[ico-circle]: https://circleci.com/gh/code4interactive/DataTable/tree/L4.svg?style=svg
[ico-downloads]: https://img.shields.io/packagist/dt/code4interactive/DataTable.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/code4interactive/DataTable

[link-travis]: https://travis-ci.org/code4interactive/DataTable
[link-scrutinizer]: https://scrutinizer-ci.com/g/code4interactive/DataTable/code-structure
[link-downloads]: https://packagist.org/packages/code4interactive/DataTable
[link-author]: https://github.com/code4interactive
[link-contributors]: ../../contributors

