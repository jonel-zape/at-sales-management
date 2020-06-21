<?php
    $tableId = 'dataTable';

    if (isset($params['id'])) {
        $tableId = $params['id'];
    }
?>

<div id="<?php echo $tableId; ?>"></div>

<script type="text/javascript">

    let <?php echo $tableId; ?> = {

        rowEditingIndex: 0,
        invalidRow: -1,
        isRowClickDisabled: false,

        tabulator: new Tabulator("#<?php echo $tableId; ?>", {
            autoColumns: false,
            pagination: "local",
            paginationSize: 10,
            reactiveData: true,
            paginationSizeSelector: [5, 10, 50, 100, 200],
            paginationAddRow:"table",
            layout: "fitColumns",
            rowClick: function(e, row) {
                let that = <?php echo $tableId; ?>;

                if (!that.isRowClickDisabled) {
                    that.rowClicked(e, row);
                }
                that.isRowClickDisabled = false;
            },
            cellEditing:function(cell){
                let that = <?php echo $tableId; ?>;

                that.invalidRow = -1;
                that.rowEditingIndex = cell.getRow().getIndex();
            },
            cellEdited: function(cell) {
                let that = <?php echo $tableId; ?>;

                if (cell.getRow().getIndex() == that.invalidRow) {
                    that.invalidRow = -1;
                }

                that.cellEdited(cell);

                $(cell.getRow().getElement()).css({
                    "background-color": "#feffb0"
                });
            },
            rowDeleted: function(row) {
                let that = <?php echo $tableId; ?>;

                if (row.getIndex() == that.invalidRow) {
                    that.invalidRow = -1;
                }
            },
            validationFailed:function(cell, value, validators){
                let that = <?php echo $tableId; ?>;
                that.invalidRow = cell.getRow().getIndex();
                that.validationFailed(cell);
            },
        }),
        setColumns(columns) {
            this.tabulator.setColumns(columns);
        },
        setData(items) {
            this.tabulator.setData(items);
        },
        getData(items) {
            return this.tabulator.getData();
        },
        deleteRow(index) {
            let that = <?php echo $tableId; ?>;

            if (index == that.invalidRow) {
                that.invalidRow = -1;
            }
            that.tabulator.deleteRow(index);
        },
        rowClicked() {

        },
        cellEdited(cell) {

        },
        validationFailed(cell) {

        },
        preventRowClick() {
            this.isRowClickDisabled = true;
        },
        hide() {
            $("#<?php echo $tableId; ?>").css("visibility", "hidden");
        },
        show() {
            $("#<?php echo $tableId; ?>").css("visibility", "visible");
        },
        autocomplete(options = {
            field        : '',
            displayResult: '',
            route        : '',
            result       : {},
            selected     : {}
        }) {
            let that = this;;

            $("#<?php echo $tableId; ?>").on('focus', '.tabulator-cell', function() {
                let field = $(this).attr('tabulator-field');

                if (field != options.field) {
                    $(this).find('input').autocomplete("destroy");
                    $(this).find('input').removeData('autocomplete');

                    return;
                }

                let originalValue = $(this).find('input').val();

                $.ajaxSetup({
                    headers: { 'X-USER-TOKEN': window.token }
                });

                $(this).find('input').autocomplete({
                    serviceUrl: options.route + '?field=' + field,
                    dataType: 'json',
                    paramName: 'keyword',
                    preserveInput: true,
                    transformResult: function(response) {
                        return {
                            suggestions: $.map(response.values, function(dataItem) {
                                return {
                                    value: dataItem[options.displayResult],
                                    data: options.result(dataItem)
                                };
                            })
                        };
                    },
                    onSelect: function (suggestion) {
                        originalValue = suggestion.data[field];
                        options.selected(suggestion.data);
                    }
                });

                $(this).find('input').blur(function(){
                    let data = [{
                        id: that.getRowEditingIndex(),
                        [field]: originalValue
                    }];
                    that.tabulator.updateData(data);
                });

            });

        },
        addInsertingRow(indetifier, items, initial) {
            let index = items.length;
            if (index > 0) {
                if (items[index - 1][indetifier] == 0) {
                    return;
                }
            }
            initial['id'] = index;
            items.push(initial);
            this.gotoLastPage();
        },
        addData(data, isOnTop) {
            this.tabulator.addData(data, isOnTop);
            this.gotoLastPage();
        },
        gotoLastPage() {
            this.tabulator.setPage(this.tabulator.getPageMax());
            this.tabulator.setPage(this.tabulator.getPageMax());
        },
        getRowEditingIndex() {
            return this.rowEditingIndex;
        },
        hasValidationError() {
            let that = <?php echo $tableId; ?>;

            return that.invalidRow > -1;
        },
        deleteIcon: function(cell, formatterParams){
            return "<i class='fa fa-times color-red'></i>";
        },
        arrowLeftIcon: function(cell, formatterParams){
            return "<i class='control-icon fa fa-arrow-left'></i>";
        },
        headerWithPencilIcon(title) {
            return "<i class=\"fa fa-pencil\" aria-hidden=\"true\"></i> " + title;
        },
        findColumnIndexByField(key, columns) {
            for (let i = columns.length - 1; i >= 0; i--) {
                if (columns[i].hasOwnProperty("field") && columns[i].field == key) {
                    return i;
                }
            }

            return -1;
        },
        getSelectedData() {
            return this.tabulator.getSelectedData();
        }
    };
</script>