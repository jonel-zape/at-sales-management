let auth = {

    login()
    {
        let that = this;

        http.post(
            '/guest/authenticate',
            {
                username: el.val("#username"),
                password: el.val("#password")
            }
        ).done(function(response){
            alert.success('Success!');
            window.location = "/home";
        }).catch(function(response){
            alert.error(response.errors);
        });
    }
};
