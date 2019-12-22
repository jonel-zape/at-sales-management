let list = {
    find() {
        loading.show();

        let that = this;
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
        let columns = [
            { title:"Stock No.", field:"stock_no", formatter:"plaintext" },
            { title:"Name", field:"name", formatter:"plaintext"},
            { title:"Short Name", field:"short_name", formatter:"plaintext" },
            { title:"Cost Price", field:"cost_price", formatter:"money" },
            { title:"Selling Price", field:"selling_price", formatter:"money"},
            { title:"Wholesale Price", field:"wholesale_price", formatter:"money"},
            { title:"Memo", field:"memo" },
        ];
        dataTable.tabulator.setColumns(columns);
    }
};

list.setDataTableColumns();
list.find();