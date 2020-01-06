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
            width    : 120,
            formatter: "money",
            align    : "right",
            editor   : "input",
            validator: ["min:1", "integer", "required"]
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
            route : '/purchase/autoCompleteSearch',
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
            route : '/purchase/autoCompleteSearch',
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

            let selling = that.products[index].selling_price;
            let qty = that.products[index].quantity;

            that.products[index].amount = selling * qty;

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
            purchase_detail_id      : item.purchase_detail_id,
            purchase_invoice_number : item.purchase_invoice_number,
            stock_no                : item.stock_no,
            short_name              : item.short_name,
            selling_price           : item.selling_price,
            quantity                : 1,
            amount                  : item.selling_price,
            remark                  : item.remark,
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

        this.products[index].detail_id               = result.detail_id;
        this.products[index].purchase_detail_id      = result.purchase_detail_id;
        this.products[index].purchase_invoice_number = result.purchase_invoice_number;
        this.products[index].stock_no                = result.stock_no;
        this.products[index].short_name              = result.short_name;
        this.products[index].selling_price           = result.selling_price;
        this.products[index].quantity                = result.quantity;
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
            quantity               : "1",
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
//detail.loadDetail();