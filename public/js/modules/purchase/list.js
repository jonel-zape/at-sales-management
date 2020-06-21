let list = {
    create() {
        window.location = "/purchase/create";
    },

    find() {
        loading.show();

        let that = this;
        http.get(
            '/purchase/find',
            {
                invoice_number: el.val("#invoice_number"),
                status        : el.val("#status"),
                date_from     : el.val("#date_from"),
                date_to       : el.val("#date_to"),
            }
        ).done(function(response){
            alert.dismiss();
            dataTable.setData(response.values);
            dataTable.show();
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            dataTable.hide();
            loading.hide();
        });
    },

    setDataTableColumns() {
        let that = this;

        let deleteIcon = function(cell, formatterParams){
            return "<i class='fa fa-times color-red'></i>";
        };

        let columns = [
            {
                formatter: "rownum",
                align    : "center",
                width    : 40,
                resizable: false
            },
            {
                formatter: function(cell, formatterParams){
                    if (cell.getRow().getData().status == 'Received') {
                        return '<i class="fa fa-circle purchase-received" aria-hidden="true"></i>';
                    }
                    return '<i class="fa fa-circle purchase-unreceived" aria-hidden="true"></i>';
                },
                field     : "status",
                align     : "center",
                headerSort: false,
                width     : 40,
                resizable : false
            },
            {
                title    : "Invoice Number",
                field    : "invoice_number",
                formatter: "plaintext",
                width    : 190
            },
            {
                title    : "Date",
                field    : "transaction_date",
                formatter: "plaintext",
                align    : "center",
                width    : 120,
                resizable: false
            },
            {
                title : "Memo",
                field : "memo",
                formatter : "plaintext",
                width    : 240
            },
            {
                title    : "Quantity",
                field    : "quantity",
                formatter: "money",
                align    : "right",
                width    : 150,
                resizable: false
            },
            {
                title    : "Remaining Qty.",
                field    : "remaining_quantity",
                formatter: "money",
                align    : "right",
                width    : 150,
                resizable: false
            },
            {
                title    : "Amount",
                field    : "amount",
                formatter: "money",
                align    : "right",
                width    : 150,
                resizable: false
            },
            {
                formatter : deleteIcon,
                width     : 40,
                resizable : false,
                align     : "center",
                headerSort: false,
                cellClick : function(e, cell){ that.delete(e, cell); }
            },
        ];

        dataTable.setColumns(columns);

        dataTable.rowClicked = function(e, row){
            window.location = "/purchase/edit/" + row.getData().id;
        };
    },

    delete(e, cell) {
        dataTable.preventRowClick();

        let that = this;

        modalConfirm.show({
            message: "Are you sure do you want to delete <strong>" + cell.getRow().getData().invoice_number + "</strong>?",
            confirmYes: function() {
                that.deleteConfirmed(cell);
            }
        });
    },

    deleteConfirmed(cell) {
        loading.show();

        let that = this;
        http.post(
            '/purchase/delete',
            {
                id: cell.getRow().getData().id
            }
        ).done(function(response){
            alert.success(cell.getRow().getData().invoice_number + " has been deleted.");
            dataTable.deleteRow(cell.getRow().getIndex());
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);

            loading.hide();
        });
    },
};

list.setDataTableColumns();
list.find();