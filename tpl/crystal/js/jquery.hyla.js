
function dbug(data) {
    console.warn(data);
}

(function ($) {

    $.hyla = function () {
        return;
    }

    /**
     *  Get obj
     */
	$.hyla.select = function (obj) {
        //return $(".obj_container span[id='" + file + "']").add(".obj_container span[idref='" + file + "']");
        obj = this.removeSuffix(obj);
        dbug(obj);
        dbug(".obj_container span[id^='" + obj + "-x']");
        return $(".obj_container span[id^='" + obj + "-x']");
//        dbug($(".obj_container span[id^='" + obj + "-x']").html());
	};

    $.hyla.removeSuffix = function (obj) {
        return obj.substr(0, obj.lastIndexOf('-x'));
    }

    /**
     *  Decode obj for send query to ws
     *
     *  See those url for explanation about this :
     *  - http://www.w3.org/TR/html4/types.html#h-6.2
     *  - http://www.w3.org/TR/REC-CSS2/grammar.html
     *  See also /tpl/default/function.php (get_object_id)
     *
     *  Ex:
     *      in:     hyla-obj--atoto-atata-a-x0
     *      out:    /toto/tata/
     *
     *  prefix :    hyla-obj-
     *  obj :       -atoto-atata-a
     *  suffix :    -xX             (where X is a number)
     *
     */
    $.hyla.decodeObj = function (obj) {

        if (obj && obj.substr(0, 9) == 'hyla-obj-') {
            obj = obj.substr(9);
            obj = obj.replace('--', '-', 'g');
            obj = obj.replace('-a', '/', 'g');
            obj = obj.replace('-b', '_', 'g');
            obj = obj.replace('-c', '\'', 'g');
            obj = obj.replace('-d', '"', 'g');
            obj = obj.replace('-e', '.', 'g');

            obj = this.removeSuffix(obj);
        }

        return obj;
    }

    /**
     *  Test if is error
     */
    $.hyla.isError = function (json) {
        return (json.status < 0) ? true : false;
    }

    /**
     *  Test and view error
     */
    $.hyla.testIfError = function (data) {

        var ret = false;

        if (this.isError(data)) {
            $.jGrowl(data.content, { header: _('Error !') });
            ret = true;
        }

        return ret;
    }

    /**
     *  Eval json
     */
    $.hyla.evalJSON = function (json) {
        eval('var ret = ' + json);
        return ret;
    }


    /**
     *  Web services
     */
    $.hyla.ws = function () {
        return;
    }

    /**
     *  Get ws url entry
     */
    $.hyla.ws.url = function (method, param) {
        var params = '&';
        for (var i in param) {
            params += i + '=' + param[i] + '&';
        }
        return _('DIR_ROOT') + 'index.php/ws/?method=' + method + params;
    }

})(jQuery);


