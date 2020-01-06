let detail = {
    products: [],

    columns: [
        {
            formatter: "rownum",
            align    : "center",
            width    : 40
        },
        {
            title    : dataTable.headerWithPencilIcon("Stock No"),
            field    : "stock_no",
            formatter: "plaintext",
            width    : 120,
            editor   : "input"
        },
        {
            title    : dataTable.headerWithPencilIcon("Short Name"),
            field    : "short_name",
            formatter: "plaintext",
            width    : 245,
            editor   : "input"
        },
        {
            title    : dataTable.headerWithPencilIcon("Cost"),
            field    : "cost_price",
            width    : 90,
            formatter: "money",
            align    : "right",
            editor   : "input",
            validator: ["min:0", "numeric", "required"]
        },
        {
            title    : dataTable.headerWithPencilIcon("Quantity"),
            field    : "quantity",
            width    : 120,
            formatter: "money",
            align    : "right",
            editor   : "input",
            validator:["min:1", "integer", "required"]
        },
        {
            title    : "Remaining",
            field    : "remaining_qty",
            width    : 120,
            formatter: "money",
            align    : "right",
            visible  : false
        },
        {
            title    : "Amount",
            field    : "amount",
            width    : 100,
            formatter: "money",
            align    : "right",
        },
        {
            title : dataTable.headerWithPencilIcon("Remark"),
            field : "remark",
            width : 200,
            editor: "input",
        },
        {
            formatter: dataTable.deleteIcon,
            width    : 40,
            align    : "center",
            cellClick: function(e, cell){ detail.delete(e, cell); }
        },
    ],

    save() {
        if (dataTable.hasValidationError()) {
            alert.error(['Please finalize row']);
            return;
        }

        if (dataTable.getData().length < 2) {
            alert.error(['Please input item(s)']);
            return;
        }

        loading.show();
        let that= this;

        let received_at = null;
        if (el.checkbox.isChecked("#is_received")) {
            received_at = el.val("#received_at");
        }

        http.post(
            '/purchase/save',
            {
                id              : el.val("#id"),
                invoice_number  : el.val("#invoice_number"),
                transaction_date: el.val("#transaction_date"),
                memo            : el.val("#memo"),
                detail          : dataTable.getData(),
                received_at     : received_at
            }
        ).done(function(response){
            alert.success('Saved.');
            window.location = '/purchase/edit/' + response.values.id;
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },

    setTable() {
        let that = this;

        dataTable.setColumns(this.columns);
        dataTable.setData(this.products);
        this.addInsertingRow();

        dataTable.autocomplete({
            field : 'stock_no',
            route : '/product/autonCompleteSearch',
            result: function(item) {
                return that.autocompleteResultFormat(item);
            },
            selected: function(result) {
                that.autocompleteOnSelected(result);
                that.getSummary();
            }
        });

        dataTable.autocomplete({
            field : 'short_name',
            route : '/product/autonCompleteSearch',
            result: function(item) {
                return that.autocompleteResultFormat(item);
            },
            selected: function(result) {
                that.autocompleteOnSelected(result);
                that.getSummary();
            }
        });

        dataTable.cellEdited = function(cell) {
            let index = cell.getRow().getIndex();

            let cost = that.products[index].cost_price;
            let qty = that.products[index].quantity;

            that.products[index].amount = cost * qty;

            that.getSummary();
        }
    },

    loadDetail() {
        loading.show();
        let that = this;

        http.get(
            '/purchase/details',
            {
                id: el.val("#id")
            }
        ).done(function(response){
            that.products = response.values.details;
            dataTable.setData(that.products);

            if (!response.values.transaction.is_received) {
                that.transactionReceived();
            } else {
                that.addInsertingRow();
            }

            that.getSummary();
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },

    transactionReceived() {
        let index = dataTable.findColumnIndexByField("stock_no", this.columns);
        this.columns[index].title = "Stock No";
        delete this.columns[index].editor;

        index = dataTable.findColumnIndexByField("short_name", this.columns);
        this.columns[index].title = "Short Name";
        delete this.columns[index].editor;

        index = dataTable.findColumnIndexByField("cost_price", this.columns);
        this.columns[index].title = "Cost";
        delete this.columns[index].editor;

        index = dataTable.findColumnIndexByField("remark", this.columns);
        this.columns[index].title = "Remark";
        delete this.columns[index].editor;

        index = dataTable.findColumnIndexByField("remaining_qty", this.columns);
        this.columns[index].visible = true;

        index = dataTable.findColumnIndexByField("quantity", this.columns);
        this.columns[index].validator = ["required", "integer", {
            type: function(cell, value, parameters) {
                return parseFloat(value) >= parseFloat(cell.getRow().getData().min_quantity);
            },
        }];

        this.columns.pop();

        dataTable.setColumns(this.columns);
    },


    delete(e, cell) {
        let that = this;

        if (cell.getRow().getData().product_id == 0) {
            return;
        }

        modalConfirm.show({
            message: "Are you sure do you want to delete <strong>" + cell.getRow().getData().name + "</strong>?",
            confirmYes: function() {
                that.deleteConfirmed(cell);
            }
        });
    },

    deleteConfirmed(cell) {
        dataTable.deleteRow(cell.getRow().getIndex());
    },

    autocompleteResultFormat(item) {
        return {
            product_id: item.product_id,
            stock_no  : item.stock_no,
            name      : item.name,
            short_name: item.short_name,
            memo      : item.memo,
            cost_price: item.cost_price,
            quantity  : 1,
            amount    : item.cost_price,
            remark    : ''
        };
    },

    autocompleteOnSelected(result) {

        let index = dataTable.getRowEditingIndex();

        let data = dataTable.getData();
        for (let i = data.length - 1; i >= 0; i--) {
            if (data[i].product_id == 0) {
                continue;
            }
            if (data[i].product_id == result.product_id && data[i].id != index) {
                alert.error(['Product cannot be duplicated.']);
                return;
            }
        }

        this.products[index].product_id = result.product_id;
        this.products[index].stock_no   = result.stock_no;
        this.products[index].name       = result.name;
        this.products[index].short_name = result.short_name;
        this.products[index].memo       = result.memo;
        this.products[index].cost_price = result.cost_price;
        this.products[index].quantity   = result.quantity;
        this.products[index].amount     = result.amount;
        this.products[index].remark     = result.remark;

        this.addInsertingRow();
    },

    addInsertingRow() {
        dataTable.addInsertingRow("product_id", this.products, {
            detail_id         : 0,
            product_id        : 0,
            stock_no          : "",
            name              : "",
            short_name        : "",
            memo              : "",
            cost_price        : "0",
            quantity          : "1",
            amount            : "0",
            remaining_quantity: "0",
            remark            : ""
        });
    },

    toggleReceived() {
        if (el.checkbox.isChecked("#is_received")) {
            el.show("#received_at_container");
        } else {
            el.hide("#received_at_container");
        }
    },

    getSummary() {
        let data = dataTable.getData();
        let totalQuantity = 0;
        let totalAmount = 0;

        for (let i = data.length - 1; i >= 0; i--) {
            if (data[i].product_id == 0) {
                continue;
            }
            totalQuantity += parseFloat(data[i].quantity);
            totalAmount += parseFloat(data[i].amount);
        }

        el.setContent("#total_quantity", number.money(totalQuantity));
        el.setContent("#total_amount", number.money(totalAmount));
    }
};

detail.setTable();
detail.loadDetail();
detail.toggleReceived();