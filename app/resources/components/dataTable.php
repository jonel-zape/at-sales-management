<div id="data-table"></div>

<script type="text/javascript">

    let dataTable = {

        tabulator: new Tabulator("#data-table", {
            autoColumns: false,
            pagination: "local",
            paginationSize: 10,
            paginationSizeSelector: [5, 10, 50, 100, 200],
            rowClick: function(e, row) {
                if (!dataTable.isRowClickDisabled) {
                    dataTable.rowClicked(e, row);
                }
                dataTable.isRowClickDisabled = false;
            }
        }),
        rowClicked: {},
        isRowClickDisabled: false,
        preventRowClick() {
            this.isRowClickDisabled = true;
        },
        hide() {
            $("#data-table").css("visibility", "hidden");
        },
        show() {
            $("#data-table").css("visibility", "visible");
        }
    };

</script>