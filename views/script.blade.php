<script>
    $(document).ready(function() {

        $('#dt-{{$name}}').dataTable({
            responsive: true,
            serverSide: true,
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
                @foreach($columns as $col)
                {
                    data: '{{$col}}',
                    orderable: {!!  in_array($col, $noSorting) ? 'false' : 'true' !!}
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