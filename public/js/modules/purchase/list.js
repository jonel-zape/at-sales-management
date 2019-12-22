let list = {
    create() {
        window.location = "/purchase/create";
    },

    find() {
        loading.show();

        let that = this;
        http.get(
            '/purchase/find',
            {}
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
            { title:"Date", field:"date", formatter:"plaintext" },
            { title:"Memo", field:"memo"},
            { title:"Status", field:"status", formatter:"plaintext" }
        ];
        dataTable.tabulator.setColumns(columns);
    }
};

list.setDataTableColumns();
list.find();