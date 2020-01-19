let detail = {
    products: [],

    columns: [
        {
            formatter: "rownum",
            align    : "center",
            width    : 40
        },
        {
            title    : "PO Invoice",
            field    : "purchase_invoice_number",
            formatter: "plaintext",
            width    : 170,
        },
        {
            title    : dataTable.headerWithPencilIcon("Stock No"),
            field    : "stock_no",
            formatter: "plaintext",
            width    : 245,
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
            title    : dataTable.headerWithPencilIcon("Price"),
            field    : "selling_price",
            width    : 120,
            formatter: "money",
            align    : "right",
            editor   : "input",
            validator: ["min:0", "numeric", "required"]
        },
        {
            title    : dataTable.headerWithPencilIcon("Quantity"),
            field    : "quantity",
            width    : 150,
            formatter: "money",
            align    : "right",
            editor   : "input",
            validator: ["min:1", "integer", "required", {
                type: function(cell, value, parameters) {
                    return parseFloat(value) <= parseFloat(cell.getRow().getData().available_quantity);
                },
            }]
        },
        {
            formatter: dataTable.arrowLeftIcon,
            width    : 50,
            align    : "center",
            cellClick: function(e, cell){ detail.putAll(e, cell); }
        },
        {
            title    : "Available Qty.",
            field    : "available_quantity",
            width    : 150,
            formatter: "money",
            align    : "right",
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

        let returnedAt = null;
        if (el.checkbox.isChecked("#is_returned")) {
            returnedAt = el.val("#returned_at");
        }

        http.post(
            '/sales/save',
            {
                id              : el.val("#id"),
                invoice_number  : el.val("#invoice_number"),
                transaction_id  : el.val("#transaction_id"),
                transaction_date: el.val("#transaction_date"),
                memo            : el.val("#memo"),
                detail          : dataTable.getData(),
                returned_at     : returnedAt
            }
        ).done(function(response){
            alert.success('Saved.');
            window.location = '/sales/edit/' + response.values.id;
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
            field        : 'stock_no',
            displayResult: 'display',
            route        : '/product/receivedAutoCompleteSearch',
            result: function(item) {
                return that.autocompleteResultFormat(item);
            },
            selected: function(result) {
                that.autocompleteOnSelected(result);
                that.getSummary();
            }
        });

        dataTable.autocomplete({
            field        : 'short_name',
            displayResult: 'display',
            route        : '/product/receivedAutoCompleteSearch',
            result: function(item) {
                return that.autocompleteResultFormat(item);
            },
            selected: function(result) {
                that.autocompleteOnSelected(result);
                that.getSummary();
            }
        });

        dataTable.cellEdited = function(cell) {
            that.computeAmount(cell);
        }
    },

    computeAmount(cell) {
        let index = cell.getRow().getIndex();

        let selling = this.products[index].selling_price;
        let qty = this.products[index].quantity;

        this.products[index].amount = selling * qty;

        this.getSummary();
    },

    loadDetail() {
        loading.show();
        let that = this;

        http.get(
            '/sales/details',
            {
                id: el.val("#id")
            }
        ).done(function(response){
            that.products = response.values.details;
            dataTable.setData(that.products);
            that.addInsertingRow();

            that.getSummary();
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },

    delete(e, cell) {
        let that = this;

        if (cell.getRow().getData().purchase_detail_id == 0) {
            return;
        }

        modalConfirm.show({
            message: "Are you sure do you want to delete <strong>" + cell.getRow().getData().short_name + "</strong>?",
            confirmYes: function() {
                that.deleteConfirmed(cell);
            }
        });
    },

    deleteConfirmed(cell) {
        dataTable.deleteRow(cell.getRow().getIndex());
    },

    putAll(e, cell) {
        if (cell.getRow().getData().purchase_detail_id == 0) {
            return;
        }

        let index = cell.getRow().getIndex();
        this.products[index].quantity = this.products[index].available_quantity;
        this.computeAmount(cell);
    },

    autocompleteResultFormat(item) {
        return {
            purchase_detail_id      : item.purchase_detail_id,
            purchase_invoice_number : item.purchase_invoice_number,
            stock_no                : item.stock_no,
            short_name              : item.short_name,
            selling_price           : item.selling_price,
            quantity                : 1,
            available_quantity      : item.available_quantity,
            amount                  : item.selling_price,
            remark                  : "",
        };
    },

    autocompleteOnSelected(result) {

        let index = dataTable.getRowEditingIndex();

        let data = dataTable.getData();
        for (let i = data.length - 1; i >= 0; i--) {
            if (data[i].purchase_detail_id == 0) {
                continue;
            }
            if (data[i].purchase_detail_id == result.purchase_detail_id && data[i].id != index) {
                alert.error(['Item cannot be duplicated.']);
                return;
            }
        }

        this.products[index].purchase_detail_id      = result.purchase_detail_id;
        this.products[index].purchase_invoice_number = result.purchase_invoice_number;
        this.products[index].stock_no                = result.stock_no;
        this.products[index].short_name              = result.short_name;
        this.products[index].selling_price           = result.selling_price;
        this.products[index].quantity                = result.quantity;
        this.products[index].available_quantity      = result.available_quantity;
        this.products[index].amount                  = result.amount;
        this.products[index].remark                  = result.remark;

        this.addInsertingRow();
    },

    addInsertingRow() {
        dataTable.addInsertingRow("purchase_detail_id", this.products, {
            detail_id              : 0,
            purchase_detail_id     : 0,
            purchase_invoice_number: "",
            stock_no               : "",
            short_name             : "",
            selling_price          : "0",
            quantity               : "0",
            available_quantity     : "0",
            amount                 : "0",
            remark                 : ""
        });
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
    },

    toggleReturned() {
        if (el.checkbox.isChecked("#is_returned")) {
            el.show("#returned_at_container");
        } else {
            el.hide("#returned_at_container");
        }
    },
};

detail.setTable();
detail.toggleReturned();
detail.loadDetail();