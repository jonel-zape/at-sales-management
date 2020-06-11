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
            title    : "Stock No",
            field    : "stock_no",
            formatter: "plaintext",
            width    : 245,
        },
        {
            title    : "Short Name",
            field    : "short_name",
            formatter: "plaintext",
            width    : 245,
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
            title    : dataTable.headerWithPencilIcon("Damaged Qty."),
            field    : "qty_damage",
            width    : 150,
            visible  : false,
            editor   : "input",
            formatter: "money",
            align    : "right",
            validator: ["min:0", "integer", "required", {
                type: function(cell, value, parameters) {
                    return parseFloat(value) <= parseFloat(cell.getRow().getData().quantity);
                }
            }]
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
                    return parseFloat(value) <= parseFloat(cell.getRow().getData().max_quantity);
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
            cellClick : function(e, cell){ detail.putAll(e, cell); }
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
            formatter : dataTable.deleteIcon,
            width     : 40,
            align     : "center",
            headerSort: false,
            cellClick : function(e, cell){ detail.delete(e, cell); }
        },
    ],

    purchaseColumns: [
        {
            formatter     : "rowSelection",
            titleFormatter: "rowSelection",
            align         : "center",
            headerSort    : false,
            width         : 20,
            cellClick     : function(e, cell) {
                cell.getRow().toggleSelect();
            }
        },
        {
            title    : "Stock No",
            field    : "stock_no",
            formatter: "plaintext",
        },
        {
            title    : "Short Name",
            field    : "short_name",
            formatter: "plaintext",
        },
        {
            title    : "Remaining",
            field    : "remaining_qty",
            formatter: "money",
            align    : "right"
        },
    ],

    save() {
        let returnedAt = null;
        if (el.checkbox.isChecked("#is_returned")) {
            returnedAt = el.val("#returned_at");
        }

        if (dataTable.hasValidationError()) {
            alert.error(['Please finalize row']);
            return;
        }

        if (dataTable.getData().length < 1) {
            alert.error(['Please input item(s)']);
            return;
        }

        let errors = [];
        for (var i = this.products.length - 1; i >= 0; i--) {
            if (this.products[i].quantity < 1) {
                errors.push("Please input quantity value for <b>" + this.products[i].short_name + "</b>");
            }

            if (parseFloat(this.products[i].available_quantity) < 0 && returnedAt == null) {
                this.products[i].quantity = this.products[i].max_quantity;
                errors.push("Cannot resell item <b>" + this.products[i].short_name + "</b> beacause it is already sold in other transaction. Please remove this item or increase its purchase invoice quantity.");
            }
        }

        if (errors.length > 0) {
            alert.error(errors);
            return;
        }

        loading.show();

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

        dataTable.cellEdited = function(cell) {
            let index = cell.getRow().getIndex();

            let remainingQty = parseFloat(that.products[index].available_quantity);
            if (remainingQty < 0) {
                that.products[index].quantity = that.products[index].max_quantity;
                alert.error(["Cannot resell item <b>" + that.products[index].short_name + "</b> beacause it is already sold in other transaction. Please remove this item or increase its purchase invoice quantity."]);
            }

            that.computeQty(cell);
            that.computeAmount(cell);
        };

        dataTable.validationFailed = function(cell) {
            let index = cell.getRow().getIndex();
            let field = cell.getColumn().getField();

            switch(field) {
                case "selling_price":
                    alert.error(["Price must be a positive numeric value."]);
                    break;
                case "quantity":
                    let maxQuantity = parseFloat(that.products[index].max_quantity);
                    alert.error(["Quantity must be a positive numeric and cannot be exceeded to purchased quantity (" + maxQuantity + ")."]);
                    break;
                case "qty_damage":
                    let quantity = parseFloat(that.products[index].quantity);
                    alert.error(["Returned quantity must be a positive numeric and cannot be exceeded to ordered quantity (" + quantity + ")."]);
                    break;
            }
        };
    },

    computeQty(cell) {
        let index = cell.getRow().getIndex();

        let quantity = parseFloat(this.products[index].quantity);
        let maxQuantity = parseFloat(this.products[index].max_quantity);
        let qtyDamage = parseFloat(this.products[index].qty_damage);

        this.products[index].available_quantity = maxQuantity - quantity;

        if (qtyDamage > quantity) {
            this.products[index].qty_damage = quantity;
        }
    },

    computeAmount(cell) {
        let index = cell.getRow().getIndex();

        let selling = this.products[index].selling_price;
        let qty = this.products[index].quantity;

        this.products[index].amount = parseFloat(selling) * parseFloat(qty);

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
            el.setContent("#status", response.values.transaction.status);
            $("#status").addClass(response.values.transaction.status_class);
            that.transactionReturnedToSeller();
            that.getSummary();
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },

    transactionReturnedToSeller() {
        if (!isReturned) {
            return;
        }

        let index = dataTable.findColumnIndexByField("qty_damage", this.columns);
        this.columns[index].visible = true;

        dataTable.setColumns(this.columns);
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
        this.computeAmount(cell);
    },

    putAll(e, cell) {
        if (cell.getRow().getData().purchase_detail_id == 0) {
            return;
        }

        let index = cell.getRow().getIndex();

        this.products[index].quantity = parseFloat(this.products[index].quantity) + parseFloat(this.products[index].available_quantity);
        this.products[index].available_quantity = 0;

        this.computeAmount(cell);
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

        if (el.val("#returned_at") == "") {
            let dateNow = new Date();
            $("#returned_at").val(`${dateNow.getFullYear()}-${dateNow.getMonth() + 1}-${dateNow.getDate()}`);
        }
    },

    purchaseInvoiceSelected() {
        loading.show();
        let that = this;
        http.get(
            '/purchase/details',
            {
                id: autocompletePurchaseNumberSelectedId
            }
        ).done(function(response){
            el.setContent("#purchaseDateReceived", response.values.transaction.received_at);
            el.setContent("#purchaseMemo", response.values.transaction.memo);

            purchaseTable.setColumns(that.purchaseColumns);
            purchaseTable.setData(response.values.details);

            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },

    importSelected() {
        let data = purchaseTable.getSelectedData();

        if (data.length < 1) {
            alertModal.error(['Please select at least one item.']);
            return;
        }

        let errors = [];
        let detailId = 0;
        for (let i = data.length - 1; i >= 0; i--) {
            for (let j = this.products.length - 1; j >= 0; j--) {
                if (this.products[j].purchase_detail_id == data[i].detail_id) {
                    errors.push("Item <b>" + data[i].short_name + "</b> already exists.");
                    break;
                }
            }
            if (parseFloat(data[i].remaining_qty) < 1) {
                errors.push("Item <b>" + data[i].short_name + "</b> is already Sold out.");
            }
        }

        if (errors.length > 0) {
            alertModal.error(errors);
            return;
        }

        let newData = [];
        let count = dataTable.getData().length;

        for (let i = 0; i < data.length; i++) {
            newData.push({
                id                     : (i + count),
                detail_id              : 0,
                purchase_detail_id     : data[i].detail_id,
                purchase_invoice_number: data[i].invoice_number,
                stock_no               : data[i].stock_no,
                short_name             : data[i].short_name,
                selling_price          : data[i].selling_price,
                max_quantity           : data[i].remaining_qty,
                qty_damage             : 0,
                quantity               : 0,
                available_quantity     : data[i].remaining_qty,
                amount                 : parseFloat(0),
                remark                 : ""
            });
        }

        this.products = [...dataTable.getData(), ...newData];
        dataTable.setData(this.products);
        dataTable.gotoLastPage();
        this.getSummary();

        $("#buttonCloseModal").click();
    }
};

detail.setTable();
detail.toggleReturned();
detail.loadDetail();