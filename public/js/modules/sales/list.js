let list = {

    data: [
        {
            id: 0,
            invoice_number: "S00000001",
            transaction_id: "432342303423234G",
            transaction_date: "2020-01-04",
            memo: "Lazada",
            quantity: 124,
            amount: 3422,
            status: "Sold"
        },
        {
            id: 1,
            invoice_number: "S00000001",
            transaction_id: "432342303423234G",
            transaction_date: "2020-01-04",
            memo: "Shopee",
            quantity: 124,
            amount: 3422,
            status: "RTS"
        }
    ],

    columns: [
        {
            formatter: "rownum",
            align    : "center",
            width    : 40
        },
        {
            title    : "Invoice Number",
            field    : "invoice_number",
            formatter: "plaintext",
            width    : 190
        },
        {
            title    : "Transaction ID",
            field    : "transaction_id",
            formatter: "plaintext",
            width    : 190
        },
        {
            title    : "Date",
            field    : "transaction_date",
            formatter: "plaintext",
            width    : 120
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
            width    : 150
        },
        {
            title    : "Amount",
            field    : "amount",
            formatter: "money",
            align    : "right",
            width    : 150
        },
        {
            title    : "Status",
            field    : "status",
            formatter: "plaintext",
            align    : "center",
            width    : 170
        },
        {
            formatter: dataTable.deleteIcon,
            width    : 40,
            align    : "center",
            cellClick: function(e, cell){ list.delete(e, cell); }
        },
    ],

    create() {
        window.location = "/sales/create";
    },

    find() {
        dataTable.setData(this.data);

        // loading.show();

        // let that = this;
        // http.get(
        //     '/sales/find',
        //     {
        //         invoice_number: el.val("#invoice_number"),
        //         status        : el.val("#status"),
        //         date_from     : el.val("#date_from"),
        //         date_to       : el.val("#date_to"),
        //     }
        // ).done(function(response){
        //     alert.dismiss();
        //     dataTable.setData(this.data);
        //     dataTable.show();
        //     loading.hide();
        // }).catch(function(response){
        //     alert.error(response.errors);
        //     dataTable.hide();
        //     loading.hide();
        // });
    },

    setDataTableColumns() {
        dataTable.setColumns(this.columns);

        dataTable.rowClicked = function(e, row){
            window.location = "/sales/edit/" + row.getData().id;
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
            '/sales/delete',
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