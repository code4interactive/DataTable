<?php

namespace Code4\DataTable;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

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

    public function __construct() {
        if ($this->name == "") die('Brak parametru $name w DataTable');
        if ($this->url == "") die('Brak parametru $url w DataTable');
        if (!is_array($this->columns)) die('Brak listy kolumn w DataTable');
    }

    /**
     * Renderuje html tabeli
     * @return \Illuminate\View\View
     */
    public function renderTable() {
        //$data = $this->data;
        $name = $this->name;
        $columns = $this->columns;
        $columnNames = $this->columnNames;
        return view('plugins.datatable.table', compact('name', 'columns', 'columnNames'));
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

        $noSorting = $this->noSorting;

        $initSortString = "";
        if (is_array($this->initSort)) {
            $initSortColumn = $this->initSort[0];
            $initSortDirection = $this->initSort[1];
            $initSortColumnIndex = array_search($initSortColumn, $this->columns);

            $initSortString = 'order: ['.$initSortColumnIndex.', "'.$initSortDirection.'"],';
        }

        $url = $this->url;
        $name = $this->name;
        $columns = $this->columns;
        $columnNames = $this->columnNames;
        return view('DataTables::script', compact('url', 'name', 'columns', 'columnNames','limits','afterDrawCallback', 'noSorting', 'initSortString'));

    }


    /**
     * Zamienia array na string umieszczając teksty w cudzysłowach.
     * @param $arr
     * @return string
     */
    public function collapseToString($arr) {
        $string = '';

        for ($lp=0; $lp<count($arr); $lp++) {

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
     * Renderuje dane uwzględniając sortowanie, wyszukiwanie, paginację oraz
     * @param Request $request
     * @return array
     */
    public function renderData($request) {

        //dd($request->only(['draw','columns','order','start','length','search']));

        $draw = (int) $request->get('draw');
        $columns = $request->get('columns');
        $order = $request->get('order');
        $start = $request->get('start');
        $length = $request->get('length') == '-1'? '1000' : $request->get('length') ;
        $search = $request->get('search');

        $orderDir = $order[0]['dir'];
        $orderCol = $this->columns[$order[0]['column']];

        $search = $search['value'];

        $data = $this->getData($start, $length, $search, $orderCol, $orderDir);

        if ($data instanceof Arrayable) {
            $data = $data->toArray();
        }

        $data = $this->decorate($data);

        $total = $this->countAll();

        $data = ["draw" => $draw,
        "recordsTotal" => $total,
        "recordsFiltered" => $total,
        "data" => $data ];

        return $data;
    }

    /**
     * Skrypty wywoływane po wypełnieniu tabeli danymi. Niektóre dekoratory mogą wymagać ponownego wykonania skryptu.
     * @return string
     */
    protected function afterDrawCallback() {
        return "";
    }

}