<?php

namespace Code4\DataTable\Traits;

use Carbon\Carbon;

trait DataTableDateDecorators
{
    public function DateToFormattedDateStringDecorator($cell, $row) {
        $dt = new Carbon($cell);
        return $dt->toFormattedDateString();
    }
}