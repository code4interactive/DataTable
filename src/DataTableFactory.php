<?php

namespace Code4\DataTable;

use Collective\Html\HtmlBuilder;

class DataTableFactory {

    /**
     * @var HtmlBuilder
     */
    protected $html;

    public function __construct(HtmlBuilder $html) {
        $this->html = $html;
    }

    /**
     * @param $instanceClass
     * @return \Code4\DataTable\DataTable;
     */
    public function make($instanceClass) {

        return new $instanceClass($this->html);

    }

    public function getInstance() {

    }

}