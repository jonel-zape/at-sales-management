let http = {

    method: '',
    url: '',
    data: {},
    doneCallBack: {},
    failCallBack: {},

    get(url, data) {
        this.method = 'GET';
        this.url = url;
        this.data = data;

        return this;
    },

    post(url, data) {
        this.method = 'POST';
        this.url = url;
        this.data = data;

        return this;
    },

    done(callback) {
        this.doneCallBack = callback;

        return this;
    },

    catch(callback) {
        this.failCallBack = callback;
        this.request(this.method, this.url, this.data, this.doneCallBack, this.failCallBack);
    },

    request(method, url, data, doneCallBack, failCallBack) {
        $.ajax({
            method: method,
            url: url,
            headers: { 'X-USER-TOKEN': 'sample token' },
            data: data,
            dataType: 'json'
        }).done(function(response) {
            doneCallBack(response);
        }).fail(function(response) {
            failCallBack(response.responseJSON);
        });
    }
};
