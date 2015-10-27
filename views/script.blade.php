<script>
    $(document).ready(function() {

        $('#dt-{{$name}}').dataTable({
            responsive: true,
            serverSide: true,
            //deferRender: true,  //speedup rendering
            dom: 'T<"clear">lfrtip',
            autoWidth: false,
            {!!$initSortString!!}
            ajax: {
                url: '{!!$url!!}',
                dataSrc: 'data',
                type: 'POST'
            },
            lengthMenu: {!!$limits!!},
            columns: [
                @foreach($columns as $column)
                {
                    data: '{{$column->getId()}}',
                    {!! $column->getWidthString() !!}
                    orderable: {!!  $column->isSortable() ? 'true' : 'false' !!}
                },
                @endforeach
                ]
        });

        $('#dt-{{$name}}').on( 'processing.dt', function ( e, settings, processing ) {
            $('.processingIndicator').css( 'display', processing ? 'block' : 'none' );
        });

        $('#dt-{{$name}}').on( 'draw.dt', function () {
            {!!$afterDrawCallback!!}
        });


    });
</script>