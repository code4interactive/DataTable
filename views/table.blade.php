<div id="dt-{{$name}}-wrapper" class="table-responsive">
    <table id="dt-{{$name}}" class="table table-striped table-bordered table-hover" >
        <thead>
        <tr>
        @foreach($columns as $col)
            <th>{{$col->title}}</th>
        @endforeach
        </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <tr>
        @foreach($columns as $col)
            <th>{{$col->title}}</th>
        @endforeach
        </tr>
        </tfoot>
    </table>
</div>