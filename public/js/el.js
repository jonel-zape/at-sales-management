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

    smoothHide(identifier) {
        $(identifier).css({
            "visibility" : "hidden",
            "opacity" : 0,
            "transition" : "visibility 0s linear 0.2s, opacity 0.2s linear"
        });
    },

    smoothShow(identifier) {
        $(identifier).css({
            "opacity" : 1,
            "display" : "flex",
            "visibility" : "visible",
            "transition" : "opacity 0.2s linear"
        });
    },

    setContent(identifier, content) {
        $(identifier).html(content);
    }
};
