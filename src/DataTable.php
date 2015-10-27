<?php

namespace Code4\DataTable;

use Collective\Html\HtmlBuilder;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class DataTable extends Decorator {

    /**
     * Unikalna nazwa tabeli - bez znaków specjalnych i spacji
     * @var
     */
    protected $name;

    /**
     * URL źródła danych
     * @var
     */
    protected $url;

    /**
     * Lista kolumn bazy danych (nazw z bazy) potrzebna wyłącznie do sortowania i wyszukiwania
     * @var
     */
    protected $columns;

    /**
     * Lista instancji klasy Columns
     */
    protected $columnsCollection;

    /**
     * Lista nazw kolumn do wyświetlenia na stronie
     * @var
     */
    protected $columnNames;

    /**
     * Lista kolumn w których ma nie być sortowania
     * @var
     */
    protected $noSorting;

    /**
     * Domyślna kolumna i kierunek sortowania
     * @var
     */
    protected $initSort;

    /**
     *  @var HtmlBuilder
     */
    protected $html;

    /**
     * Przyjmuje tablice asocjacyjną lub zwykła. W asocjacyjnej wartość reprezentuje opis zakresu
     * Maksymalna wartość przy -1 to 10000 wierszy danych
     * @var array
     */
    protected $limits = [
        10 => "10",
        25 => "25",
        50 => "50",
        -1 => "Max"
    ];

    public function __construct(HtmlBuilder $html) {
        if ($this->name == "") throw new \Exception('Attribute $name is missing');
        if ($this->url == "") throw new \Exception('Attribute protected $url is missing');
        if (!is_array($this->columns)) throw new \Exception('Attribute (array) protected $columns is missing or wrong type');

        $this->html = $html;
        $this->columnsCollection = new Collection();

        foreach($this->columns as $column => $attributes) {
            $attributes = is_array($attributes) ? $attributes : ['title' => $attributes];
            $this->columnsCollection->put($column, new Column($column, $attributes, $this->html));
        }
    }

    /**
     * Zwraca do edycji wybraną kolumnę
     * @param $name
     * @return mixed
     */
    public function column($name) {
        return $this->columnsCollection->get($name);
    }


    /**
     * Renderuje html tabeli
     * @return \Illuminate\View\View
     */
    public function renderTable() {
        return view('DataTable::table', ['name' => $this->name, 'columns' => $this->columnsCollection]);
    }

    /**
     * Renderuje skrypt dla tabeli
     * @return \Illuminate\View\View
     */
    public function renderScript() {

        if (isAssoc($this->limits)) {
            $keys = array_keys($this->limits);
            $values = array_values($this->limits);
            $limits = '[['.implode(',',$keys).'],['.$this->collapseToString($values).']]';
        } else {
            $limits = '[['.implode(',',$this->limits).'],['.$this->collapseToString($this->limits).']]';
        }

        $afterDrawCallback = $this->afterDrawCallback();

        /** Generacja stringu początkowego sortowania **/
        $colCount = 0;
        $initSortString = "";
        foreach($this->columnsCollection as $column) {
            if (isset($column->sort)) {
                $initSortDirection = $column->sort;
                $initSortColumnIndex = $colCount;
                $initSortString .= '['.$initSortColumnIndex.', "'.$initSortDirection.'"],';
            }
            $colCount++;
        }
        if ($initSortString != "") {
            $initSortString = rtrim($initSortString, ',');
            $initSortString = 'order: [ ' . $initSortString . ' ],';
        }

        $url = $this->url;
        $name = $this->name;
        $columns = $this->columnsCollection;
        return view('DataTable::script', compact('url', 'name', 'columns','limits','afterDrawCallback', 'initSortString'));
    }

    /**
     * Zamienia array na string umieszczając teksty w cudzysłowach.
     * @param $arr
     * @return string
     */
    public function collapseToString($arr) {
        $string = '';
        $arrayCount = count($arr);
        for ($lp=0; $lp<$arrayCount; $lp++) {
            $item = $arr[$lp];
            if (filter_var($item, FILTER_VALIDATE_INT) !== false) {
                $string .= $item.',';
            } else {
                $string .= '"'.$item.'",';
            }
        }
        return rtrim($string, ',');
    }

    /**
     *  Funkcja musi zwracać arraya z danymi do tabeli w formacie [["key"=>"value","key"=>"value"],[..],..]
     *  Do funkcji przesyłane są dane potrzebne do odpowiedniego stronicowania, wyszukiwania itp.
     *
     * @param $start
     * @param $length
     * @param $search
     * @param $orderCol
     * @param $orderDir
     * @return mixed
     */
    abstract protected function getData($start, $length, $search, $orderCol, $orderDir);

    /**
     * Funkcja powinna zwracać Int z ilością wszystkich wyników z bazy bez uwzględnienia stronicowania itp.
     * @return int
     */
    abstract protected function countAll();

    /**
     * Funkcja pozwala na wykonanie dodatkowych operacji na obiekcie zanim zostanie on wyrenderowany
     */
    abstract protected function beforeRender();

    /**
     * Renderuje dane uwzględniając sortowanie, wyszukiwanie, paginację oraz
     * @param Request $request
     * @return array
     */
    public function renderData($request) {

        $this->beforeRender();

        $draw = (int) $request->get('draw');
        //$columns = $request->get('columns');
        $order = $request->get('order');
        $start = $request->get('start');
        $length = $request->get('length') == '-1' ? '1000' : $request->get('length') ;
        $search = $request->get('search');

        $orderDir = $order[0]['dir'];
        $orderCol = array_keys($this->columns)[$order[0]['column']];
        $search = $search['value'];

        $data = $this->getData($start, $length, $search, $orderCol, $orderDir);

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = $this->parseColumns($data);

        $data = $this->decorate($data);

        $total = $this->countAll();

        $data = ["draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => count($data),
        "data" => $data ];

        return $data;
    }

    /**
     * Przetwarza kolumny dodając brakujące oraz modyfikując ich zawartość
     * @param $data
     * @return array
     */
    public function parseColumns($data) {
        $outData = [];
        foreach($data as $row) {

            $row = $this->fixMissingCols($row);

            foreach($this->columns as $column => $attr) {

                $columnObject = $this->columnsCollection->get($column);

                //Default jeżeli pole jest puste
                if (isset($columnObject->defaultContent) && ($row[$column] == '' || $row[$column] == null)) {
                    $row[$column] = $columnObject->defaultContent;
                }

                //Overwrite jeżeli jest ustawiony
                if (isset($columnObject->content)) {
                    $row[$column] = $columnObject->content;
                }
            }
            $outData[] = $row;
        }

        return $outData;
    }

    /**
     * Funkcja może zwracać funkcję JS do wywołania po każdym rysowniu tabeli
     * @return string
     */
    public function afterDrawCallBack() {
        return '';
    }

}