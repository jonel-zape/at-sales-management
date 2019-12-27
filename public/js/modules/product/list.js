let list = {
    find() {
        loading.show();

        http.get(
            '/product/find',
            {
                keyword: el.val("#keyword"),
                filterBy: el.val("#filterBy")
            }
        ).done(function(response){
            alert.dismiss();
            dataTable.tabulator.setData(response.values);
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
            { formatter: "rownum", align: "center", width: 40},
            { title: "Stock No.", field: "stock_no", formatter: "plaintext", width: 120},
            { title: "Name", field:"name", formatter: "plaintext", width: 300},
            { title: "Short Name", field: "short_name", formatter: "plaintext", width: 120},
            { title: "Cost Price", field: "cost_price", formatter: "money", align: "right"},
            { title: "Selling Price", field: "selling_price", formatter:"money", align: "right"},
            { title: "Wholesale Price", field: "wholesale_price", formatter: "money", align: "right"},
            { title: "Memo", field: "memo", width: 220},
            { formatter: deleteIcon, width: 40, align: "center", cellClick: function(e, cell){ that.delete(e, cell); }},
        ];
        dataTable.tabulator.setColumns(columns);

        dataTable.rowClicked = function(e, row){
            window.location = "/product/edit/" + row.getData().id;
        };
    },

    delete(e, cell) {
        dataTable.preventRowClick();

        let that = this;

        modalConfirm.show({
            message: "Are you sure do you want to delete <strong>" + cell.getRow().getData().name + "</strong>?",
            confirmYes: function() {
                that.deleteConfirmed(cell)
            }
        });
    },

    deleteConfirmed(cell) {
        http.get(
            '/product/delete',
            {
                id: cell.getRow().getData().id,
            }
        ).done(function(response){
            dataTable.tabulator.deleteRow(cell.getRow().getIndex());
            alert.success("<strong>" + cell.getRow().getData().name + "</strong> has been deleted.");
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    }
};

list.setDataTableColumns();
list.find();