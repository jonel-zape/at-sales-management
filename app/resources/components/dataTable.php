<div id="data-table"></div>

<script type="text/javascript">

    let dataTable = {
        tabulator: new Tabulator("#data-table", {
            layout: "fitDataStretch",
            autoColumns: false,
            pagination: "local",
            paginationSize: 10,
            paginationSizeSelector: [5, 10, 50, 100, 200],
        }),
        hide() {
            $("#data-table").css("visibility", "hidden");
        },
        show() {
            $("#data-table").css("visibility", "visible");
        }
    };

</script>