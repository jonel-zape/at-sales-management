<div id="data-table"></div>

<script type="text/javascript">

    let dataTable = {

        rowEditingIndex: 0,
        invalidRow: -1,
        isRowClickDisabled: false,

        tabulator: new Tabulator("#data-table", {
            autoColumns: false,
            pagination: "local",
            paginationSize: 10,
            reactiveData: true,
            paginationSizeSelector: [5, 10, 50, 100, 200],
            paginationAddRow:"table",
            rowClick: function(e, row) {
                if (!dataTable.isRowClickDisabled) {
                    dataTable.rowClicked(e, row);
                }
                dataTable.isRowClickDisabled = false;
            },
            cellEditing:function(cell){
                dataTable.invalidRow = -1;
                dataTable.rowEditingIndex = cell.getRow().getIndex();
            },
            cellEdited: function(cell) {
                if (cell.getRow().getIndex() == dataTable.invalidRow) {
                    dataTable.invalidRow = -1;
                }

                dataTable.cellEdited(cell);

                $(cell.getRow().getElement()).css({
                    "background-color": "#feffb0"
                });
            },
            rowDeleted: function(row) {
                if (row.getIndex() == dataTable.invalidRow) {
                    dataTable.invalidRow = -1;
                }
            },
            validationFailed:function(cell, value, validators){
                dataTable.invalidRow = cell.getRow().getIndex();
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
            if (index == dataTable.invalidRow) {
                dataTable.invalidRow = -1;
            }
            dataTable.tabulator.deleteRow(index);
        },
        rowClicked() {

        },
        cellEdited(cell) {

        },
        preventRowClick() {
            this.isRowClickDisabled = true;
        },
        hide() {
            $("#data-table").css("visibility", "hidden");
        },
        show() {
            $("#data-table").css("visibility", "visible");
        },
        autocomplete(options = {
            field   : '',
            route   : '',
            result  : {},
            selected: {}
        }) {
            let that = this;;

            $("#data-table").on('focus', '.tabulator-cell', function() {
                let field = $(this).attr('tabulator-field');

                if (field != options.field) {
                    $(this).find('input').autocomplete("destroy");
                    $(this).find('input').removeData('autocomplete');

                    return;
                }

                let originalValue = $(this).find('input').val();

                $(this).find('input').autocomplete({
                    serviceUrl: options.route + '?field=' + field,
                    dataType: 'json',
                    paramName: 'keyword',
                    preserveInput: true,
                    transformResult: function(response) {
                        return {
                            suggestions: $.map(response.values, function(dataItem) {
                                return {
                                    value: dataItem[field],
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
            this.tabulator.setPage(this.tabulator.getPageMax());
            this.tabulator.setPage(this.tabulator.getPageMax());
        },
        getRowEditingIndex() {
            return this.rowEditingIndex;
        },
        hasValidationError() {
            return dataTable.invalidRow > -1;
        },
        deleteIcon: function(cell, formatterParams){
            return "<i class='fa fa-times color-red'></i>";
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
        }
    };
</script>