<?php

namespace Code4\DataTable;

abstract class Decorator {

    protected $columns;

    protected $cellDecorators = [];

    protected $rowDecorators = [];

    /**
     * Dekoruje dane
     * @param $data
     * @return array
     */
    public function decorate($data) {

        $result = [];

        foreach($data as $row) {
            $newRow = [];

            //Sprawdzamy czy nie brakuje kolumny
            $row = $this->fixMissingCols($row);

            //Dekorujemy wiersz
            $row = $this->decorateRow($row);

            //Dekorujemy komórki
            foreach($row as $cellName=>$cell) {
                $newRow[$cellName] = $this->decorateCell($cellName, $cell, $row);
            }

            $result[] = $newRow;
        }

        return $result;
    }

    /**
     * Dodaje brakujące kolumny do tablicy aby można było do nich później dodać dekoratory
     * @param array $row
     * @return array
     */
    protected function fixMissingCols($row) {
        foreach($this->columns as $column=>$attribs) {
            if (!array_key_exists($column, $row)) {
                $row[$column] = null;
            }
        }
        return $row;
    }

    /**
     * Dekoruje cały wiersz danych za pomocą zgłoszonych dekoratorów
     * @param $row
     * @return mixed
     */
    public function decorateRow($row) {
        foreach($this->rowDecorators as $decoratorName) {
            if (method_exists($this, $decoratorName.'Decorator')) {
                $row = call_user_func( array($this, $decoratorName.'Decorator'), $row );
            }
        }
        return $row;
    }

    /**
     * Dekoruje komórkę danych za pomocą zgłoszonych dekoratorów
     * @param $cellName
     * @param $cell
     * @param $row
     * @return mixed
     */
    public function decorateCell($cellName, $cell, $row) {
        if (array_key_exists($cellName, $this->cellDecorators)) {
            foreach($this->cellDecorators[$cellName] as $decoratorName) {
                if (method_exists($this, $decoratorName.'Decorator')) {
                    $cell = call_user_func( array($this, $decoratorName.'Decorator'), $cell, $row );
                }
            }
        }
        return $cell;
    }
}