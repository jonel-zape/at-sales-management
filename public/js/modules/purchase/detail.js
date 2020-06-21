let detail = {
    isReceived: {
        value: false
    },
    delays: {
        sellModal: null,
        salesTransaction: null
    },
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
            title    : "Sold",
            field    : "min_quantity",
            width    : 120,
            formatter: "money",
            align    : "right",
            visible  : false
        },
        {
            title    : "Damaged",
            field    : "qty_damage",
            width    : 120,
            formatter: "money",
            align    : "right",
            visible  : false
        },
        {
            formatter: function(cell, formatterParams){
                return "<i class='fa fa-external-link' aria-hidden='true' title='Show Transactions'></i>";
            },
            width    : 40,
            align    : "center",
            headerSort: false,
            cellClick: function(e, cell){ detail.showSalesDetailModal(e, cell); }
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
            headerSort: false,
            cellClick: function(e, cell){ detail.delete(e, cell); }
        },
    ],

    salesColumns: [
        {
            title    : "Stock No",
            field    : "stock_no",
            formatter: "plaintext",
            width    : 130,
        },
        {
            title    : "Short Name",
            field    : "short_name",
            formatter: "plaintext",
            width    : 130,
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
            title    : dataTable.headerWithPencilIcon("Qty."),
            field    : "quantity",
            width    : 120,
            formatter: "money",
            align    : "right",
            editor   : "input",
            validator: ["min:1", "integer", "required", {
                type: function(cell, value, parameters) {
                    return parseFloat(value) <= parseFloat(cell.getRow().getData().remaining_qty);
                }
            }]
        },
        {
            title    : "Available Qty.",
            field    : "remaining_qty",
            width    : 150,
            formatter: "money",
            align    : "right",
        },
        {
            title    : "Amount",
            field    : "selling_amount",
            width    : 100,
            formatter: "money",
            align    : "right",
        },
        {
            formatter : dataTable.deleteIcon,
            width     : 40,
            align     : "center",
            headerSort: false,
            cellClick : function(e, cell){ detail.salesDelete(e, cell); }
        }
    ],

    salesTransactionColumns: [
        {
            formatter: function(cell, formatterParams){
                if (cell.getRow().getData().sales_status == 'sold') {
                    return '<i class="fa fa-circle sales-sold" aria-hidden="true"></i>';
                }
                return '<i class="fa fa-circle sales-rts" aria-hidden="true"></i>';
            },
            field     : "status",
            align     : "center",
            headerSort: false,
            width     : 40
        },
        {
            title    : "Invoice No.",
            field    : "invoice_number",
            formatter: "plaintext",
            width    : 150
        },
        {
            formatter: function(cell, formatterParams){
                return "<i class='fa fa-external-link' aria-hidden='true' title='View Sales'></i>";
            },
            width    : 40,
            align    : "center",
            headerSort: false,
            cellClick: function(e, cell){ detail.gotoSales(e, cell); }
        },
        {
            title    : "Date",
            field    : "transaction_date",
            width    : 100,
            align    : "center",
            formatter: "plaintext",
        },
        {
            title    : "Sold",
            field    : "qty_sold",
            width    : 110,
            formatter: "money",
            align    : "right",
        },
        {
            title    : "Returned",
            field    : "qty_returned",
            width    : 110,
            formatter: "money",
            align    : "right",
        },
        {
            title    : "Damaged",
            field    : "qty_damaged",
            width    : 110,
            formatter: "money",
            align    : "right",
        }
    ],

    save() {
        let received_at = null;
        if (el.checkbox.isChecked("#is_received")) {
            received_at = el.val("#received_at");
        }

        if (dataTable.hasValidationError()) {
            alert.error(['Please finalize row']);
            return;
        }

        let minimumRow = this.isReceived.value ? 1 : 2;
        if (dataTable.getData().length < minimumRow) {
            alert.error(['Please input item(s)']);
            return;
        }

        let errors = [];
        for (let i = this.products.length - 1; i >= 0; i--) {
            if (parseFloat(this.products[i].min_quantity) > 0 && received_at == null) {
                errors.push("Cannot unreceive purhcase because some items are already sold. You can unreceive unsold items by inputting zero in desired item quantity field.");
                break;
            }
        }
        if (errors.length > 0) {
            alert.error(errors);
            return;
        }

        loading.show();
        let that= this;

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
            field        : 'stock_no',
            displayResult: 'stock_no',
            route        : '/product/autonCompleteSearch',
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
            displayResult: 'short_name',
            route        : '/product/autonCompleteSearch',
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

        salesTable.setColumns(this.salesColumns);
        salesTable.cellEdited = function(cell) {
            cell.getRow().getData().selling_amount = parseFloat(cell.getRow().getData().quantity) *  parseFloat(cell.getRow().getData().selling_price);
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

            el.setContent("#status", response.values.transaction.status);
            $("#status").addClass(response.values.transaction.status_class);

            if (response.values.transaction.is_received) {
                that.isReceived.value = true;
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

        index = dataTable.findColumnIndexByField("min_quantity", this.columns);
        this.columns[index].visible = true;

        index = dataTable.findColumnIndexByField("quantity", this.columns);
        this.columns[index].validator = ["required", "integer", {
            type: function(cell, value, parameters) {
                return parseFloat(value) >= parseFloat(cell.getRow().getData().min_quantity);
            },
        }];

        index = dataTable.findColumnIndexByField("qty_damage", this.columns);
        this.columns[index].visible = true;

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
            min_quantity      : "0",
            remark            : ""
        });
    },

    toggleReceived() {
        if (el.checkbox.isChecked("#is_received")) {
            el.show("#received_at_container");
        } else {
            el.hide("#received_at_container");
        }

        if (el.val("#received_at") == "") {
            let dateNow = new Date();
            $("#received_at").val(`${dateNow.getFullYear()}-${dateNow.getMonth() + 1}-${dateNow.getDate()}`);
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
    },

    loadSalesModal() {
        loading.show();
        http.get(
            '/purchase/details',
            {
                id: el.val("#id"),
                filterSellable: true,
            }
        ).done(function(response){
            detail.delays.sellModal = setInterval(function(){
                let items = [];
                response.values.details.forEach(function(item){
                    item.quantity = parseFloat(item.remaining_qty);
                    item.selling_amount = parseFloat(item.quantity) * parseFloat(item.selling_price);
                    items.push(item);
                });
                salesTable.setData(items);
                clearInterval(detail.delays.sellModal);
                loading.hide();
            }, 500);
        }).catch(function(response){
            alertModal.error(response.errors);
            loading.hide();
        });
    },

    salesDelete(e, cell) {
        let that = this;

        if (cell.getRow().getData().product_id == 0) {
            return;
        }

        modalConfirm.show({
            message: "Are you sure do you want to delete <strong>" + cell.getRow().getData().short_name + "</strong>?",
            confirmYes: function() {
                that.salesDeleteConfirmed(cell);
            }
        });
    },

    salesDeleteConfirmed(cell) {
        salesTable.deleteRow(cell.getRow().getIndex());
    },

    sellItems() {
        loading.show();

        let items = [];
        salesTable.getData().forEach(function(item){
            items.push({
                detail_id          : 0,
                purchase_detail_id : item.detail_id,
                quantity           : item.quantity,
                selling_price      : item.selling_price,
                remark             : ''
            });
        });

        http.post(
            '/sales/save',
            {
                id              : 0,
                invoice_number  : '',
                transaction_id  : el.val("#sales_transaction_id"),
                transaction_date: el.val("#sales_transaction_date"),
                memo            : el.val("#sales_memo"),
                detail          : items,
                returned_at     : ''
            }
        ).done(function(response){
            alertModal.success('Saved.');
            window.location = '/sales/edit/' + response.values.id;
        }).catch(function(response){
            alertModal.error(response.errors);
            loading.hide();
        });
    },

    showSalesDetailModal(e, cell) {
        loading.show();
        let detailId = cell.getRow().getData().detail_id;
        $("#show-sales-button").click();

        http.get(
            '/purchase/salesTransaction',
            {
                purchase_detail_id: detailId
            }
        ).done(function(response){
            detail.delays.salesTransaction = setTimeout(function(){
                salesDetailsTable.setColumns(detail.salesTransactionColumns);
                salesDetailsTable.setData(response.values);
                clearInterval(detail.delays.salesTransaction);
                loading.hide();
            }, 500);
        }).catch(function(response){
            alertModalSalesDetail.error(response.errors);
            loading.hide();
        });
    },

    gotoSales(e, cell) {
        loading.show();
        let id = cell.getRow().getData().sales_id;
        window.location = `/sales/edit/${id}`;
    }
};

detail.setTable();
detail.loadDetail();
detail.toggleReceived();