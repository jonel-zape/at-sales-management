let el = {
    val(identifier) {
        return $(identifier).val();
    },

    checkbox: {
        isChecked(identifier) {
            return $(identifier).prop("checked") == true;
        },

        check(identifier) {
            $(identifier).prop("checked", true)
        },

        uncheck(identifier) {
            $(identifier).prop("checked", false)
        }
    },

    hide(identifier) {
        $(identifier).css({"display" : "none"});
    },

    show(identifier) {
        $(identifier).css({"display" : "block"});
    },

    setContent(identifier, content) {
        $(identifier).html(content);
    }
};
