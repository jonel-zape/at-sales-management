let detail = {
    save() {
        loading.show();

        http.post(
            '/product/save',
            {
                id             : el.val("#id"),
                stock_no       : el.val("#stock_no"),
                name           : el.val("#name"),
                short_name     : el.val("#short_name"),
                memo           : el.val("#memo"),
                cost_price     : el.val("#cost_price"),
                selling_price  : el.val("#selling_price"),
                wholesale_price: el.val("#wholesale_price")
            }
        ).done(function(response){
            alert.success(response.values);
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    }
};