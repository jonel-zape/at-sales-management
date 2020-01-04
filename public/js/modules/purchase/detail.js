let detail = {
    products: [],

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

        let deleteIcon = function(cell, formatterParams){
            return "<i class='fa fa-times color-red'></i>";
        };

        let columns = [
            {
                formatter: "rownum",
                align    : "center",
                width    : 40
            },
            {
                title    : "Stock No.",
                field    : "stock_no",
                formatter: "plaintext",
                width    : 120,
                editor   : "input"
            },
            /* {
                title    : "Name",
                field    :"name",
                formatter: "plaintext",
                width    : 300,
                editor   : "input"
            }, */
            {
                title    : "Short Name",
                field    : "short_name",
                formatter: "plaintext",
                width    : 255,
                editor   : "input"
            },
            /* {
                title: "Memo",
                field: "memo",
                width: 220
            }, */
            {
                title    : "Cost",
                field    : "cost_price",
                width    : 80,
                formatter: "money",
                align    : "right",
                editor   : "input",
                validator: ["min:0", "numeric"]
            },
            {
                title    : "Quantity",
                field    : "quantity",
                width    : 100,
                formatter: "money",
                align    : "right",
                editor   : "input",
                validator:["min:1", "integer"]
            },
            {
                title    : "Amount",
                field    : "amount",
                width    : 100,
                formatter: "money",
                align    : "right",
            },
            {
                title : "Remark",
                field : "remark",
                width : 220,
                editor: "input",
            },
            {
                formatter: deleteIcon,
                width    : 40,
                align    : "center",
                cellClick: function(e, cell){ that.delete(e, cell); }
            },
        ];

        dataTable.setColumns(columns);
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