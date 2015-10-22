<div id="dt-{{$name}}-wrapper" class="table-responsive">
    <table id="dt-{{$name}}" class="table table-striped table-bordered table-hover" >
        <thead>
        <tr>
        @foreach($columnNames as $columnName)
            <th>{{$columnName}}</th>
        @endforeach
        </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <tr>
        @foreach($columnNames as $columnName)
            <th>{{$columnName}}</th>
        @endforeach
        </tr>
        </tfoot>
    </table>
</div>