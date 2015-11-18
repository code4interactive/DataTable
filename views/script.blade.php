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
                ],
            language: {
                "sProcessing":   "Przetwarzanie...",
                "sLengthMenu":   "Pokaż _MENU_ pozycji",
                "sZeroRecords":  "Nie znaleziono pasujących pozycji",
                "sInfoThousands":  " ",
                "sInfo":         "Pozycje od _START_ do _END_ z _TOTAL_ łącznie",
                "sInfoEmpty":    "Pozycji 0 z 0 dostępnych",
                "sInfoFiltered": "(filtrowanie spośród _MAX_ dostępnych pozycji)",
                "sInfoPostFix":  "",
                "sSearch":       "Szukaj:",
                "sUrl":          "",
                "oPaginate": {
                    "sFirst":    "Pierwsza",
                    "sPrevious": "Poprzednia",
                    "sNext":     "Następna",
                    "sLast":     "Ostatnia"
                },
                "sEmptyTable":     "Brak danych",
                "sLoadingRecords": "Wczytywanie...",
                "oAria": {
                    "sSortAscending":  ": aktywuj, by posortować kolumnę rosnąco",
                    "sSortDescending": ": aktywuj, by posortować kolumnę malejąco"
                }
            }
        });

        $('#dt-{{$name}}').on( 'processing.dt', function ( e, settings, processing ) {
            $('.processingIndicator').css( 'display', processing ? 'block' : 'none' );
        });

        $('#dt-{{$name}}').on( 'draw.dt', function () {
            {!!$afterDrawCallback!!}
        });


    });
</script>