let list = {
    create() {
        window.location = "/purchase/create";
    },

    find() {
        loading.show();

        let that = this;
        http.get(
            '/payment/find',
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
        let columns = [
            {
                formatter: "rownum",
                align    : "center",
                width    : 40
            },
            {
                formatter: function(cell, formatterParams){
                    let paidAmount = parseFloat(cell.getRow().getData().paid_amount);
                    let amountToPay = parseFloat(cell.getRow().getData().amount_to_pay);
                    let balance = parseFloat(cell.getRow().getData().balance);

                    if (balance == 0) {
                        return '<i class="fa fa-circle payment-paid" aria-hidden="true"></i>';
                    }
                    if (paidAmount == 0) {
                        return '<i class="fa fa-circle payment-unpaid" aria-hidden="true"></i>';
                    }
                    if (balance > 0 && paidAmount != amountToPay) {
                        return '<i class="fa fa-circle payment-incomplete" aria-hidden="true"></i>';
                    }

                    return '<i class="fa fa-circle payment-excess" aria-hidden="true"></i>';
                },
                field     : "status",
                align     : "center",
                headerSort: false,
                width     : 40
            },
            {
                title    : "Invoice Number",
                field    : "invoice_number",
                formatter: "plaintext",
                width    : 190
            },
            {
                formatter: function(cell, formatterParams){
                    return "<i class='fa fa-external-link' aria-hidden='true' title='View Purchase'></i>";
                },
                width    : 40,
                align    : "center",
                headerSort: false,
                cellClick: function(e, cell){ list.gotPurchase(e, cell); }
            },
            {
                title    : dataTable.headerWithPencilIcon("Paid Amount"),
                field    : "paid_amount",
                formatter: "money",
                align    : "right",
                editor   : "input",
                width    : 150,
                validator: ["min:0", "numeric", "required", {
                    type: function(cell, value, parameters) {
                        return parseFloat(value) <= parseFloat(cell.getRow().getData().amount_to_pay);
                    }
                }]
            },
            {
                formatter : dataTable.arrowLeftIcon,
                width     : 50,
                field     : "put_all",
                align     : "center",
                headerSort: false,
                visible   : true,
                cellClick : function(e, cell){ list.putAll(e, cell); }
            },
            {
                title    : "Amount to Pay",
                field    : "amount_to_pay",
                formatter: "money",
                align    : "right",
                width    : 150
            },
            {
                title    : "Balance",
                field    : "balance",
                formatter: "money",
                align    : "right",
                width    : 150
            },
            {
                title    : "Date Paid",
                field    : "date_paid",
                formatter: "plaintext",
                align    : "center",
                width    : 150
            },
            {
                title    : "Date Purchased",
                field    : "transaction_date",
                formatter: "plaintext",
                align    : "center",
                width    : 150
            }
        ];

        dataTable.setColumns(columns);
    },

    putAll(e, cell) {
        cell.getRow().getData().paid_amount = parseFloat(cell.getRow().getData().amount_to_pay) + 0;
        this.pay(cell);
    },

    pay(cell) {
        loading.show();
        http.post(
            '/payment/pay',
            {
                id: cell.getRow().getData().id,
                paid_amount: cell.getRow().getData().paid_amount
            }
        ).done(function(response){
            cell.getRow().getData().date_paid = response.values[0].paid_at;
            cell.getRow().getData().balance = parseFloat(cell.getRow().getData().amount_to_pay) - cell.getRow().getData().paid_amount;
            list.setDataTableColumns();
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },

    gotPurchase(e, cell) {
        loading.show();
        let id = cell.getRow().getData().id;
        window.location = `/purchase/edit/${id}`;
    }
};

list.setDataTableColumns();
list.find();

dataTable.cellEdited = function(cell) {
    list.pay(cell);
}