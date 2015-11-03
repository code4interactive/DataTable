<?php
namespace Code4\DataTable;

use Illuminate\Support\Fluent;

class Column extends Fluent {

    protected $id;
    /**
     * @var HtmlBuilder
     */
    protected $html;

    public function __construct($id, $attributes, $html) {
        $this->id = $id;
        $this->html = $html;

        $attributes['title'] = array_key_exists('title', $attributes) ? $attributes['title'] : $this->id;
        $attributes['sort'] = array_key_exists('sort', $attributes) ? $attributes['sort'] : null;
        $attributes['orderable'] = array_key_exists('orderable', $attributes) ? $attributes['orderable'] : true;
        $attributes['searchable'] = array_key_exists('searchable', $attributes) ? $attributes['searchable'] : true;

        parent::__construct($attributes);
    }

    /**
     * Add checkbox column
     * @param array $attributes
     */
    public function addCheckbox($attributes=[]){
        $this->title = '<input type="checkbox" ' . $this->html->attributes($attributes) . '/>';
        $this->content = '<input type="checkbox" ' . $this->html->attributes($attributes) . '/>';
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Zwraca czy kolumna ma być sortowana
     * @return bool
     */
    public function isSortable() {
        return isset($this->orderable) && $this->orderable === true ? true : false;
    }

    /**
     * Zwraca string dla script blade
     * @return string
     */
    public function getWidthString() {
        if (isset($this->width)) {
            return "width: '".$this->width."', ";
        }
        return '';
    }

}