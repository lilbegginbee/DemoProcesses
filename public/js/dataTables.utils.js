var DataTables = {
    initDefault: {
        "oLanguage": {
            "sUrl": "/js/2.0/datatables.ru.js"
        }
		,"bProcessing": true
		,"bServerSide": true
		,"sDom": "t<'panel panel-default tableBottom'<'panel-body'<'col-xs-3'l><'col-xs-3'i><'col-xs-6'p>>>"

    },
    makeDropdown: function( title, actions ) {
            var dropdown = '<div class="btn-group">'+
            '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">'+
            title + ' <span class="caret"></span>'+
            '</button>' +
            '<ul class="dropdown-menu" role="menu">';

            for(var i = 0; i<actions.length; i++) {
                if (actions[i] != 'divider') {
                    dropdown += '<li>'+actions[i]+'</li>';
                } else {
                    dropdown += '<li class="divider"></li>';
                }
            }

            dropdown += '</ul></div>';

        return dropdown;
    },

    /* когда количество столбцов разное, а опции одни и те же */
    iterateColumnsSameOptions: function(columnsQty, options, arr){
        for(i=0;i<columnsQty;i++) {
            arr.push(options);
        }
        return arr;
    }
}
/* DataTables INIT */
jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    /* dd.mm.yyyy */
    "date-ru-pre": function ( a ) {
        var res = '';
        if (a.match(/^\d+\.\d+\.\d+$/)) {
            // days
            var ruDatea = a.split('.');
            return (ruDatea[2] + ruDatea[1] + ruDatea[0]) * 1;
        } else if (res = a.match(/(\d+\.\d+\.\d+)-(\d+\.\d+\.\d+)/)) {

            // weeks
            var ruDatea1 = res[1].split('.');
            var ruDatea2 = res[2].split('.');
            return ((ruDatea1[2] + ruDatea1[1] + ruDatea1[0]) * 1) + '' + ((ruDatea2[2] + ruDatea2[1] + ruDatea2[0]) * 1);
        } else if (res = a.match(/([^,]+),\s?(\d{4})/)) {
            // month
            // @todo redo: дичайший хак, стыдно, но что сделать за 5 минут
            var index = 0;
            switch(res[1]) {
                case 'Январь':
                    index = 1;
                    break;
                case 'Февраль':
                    index = 2;
                    break;
                case 'Март':
                    index = 3;
                    break;
                case 'Апрель':
                    index = 4;
                    break;
                case 'Май':
                    index = 5;
                    break;
                case 'Июнь':
                    index = 6;
                    break;
                case 'Июль':
                    index = 7;
                    break;
                case 'Август':
                    index = 8;
                    break;
                case 'Сентябрь':
                    index = 9;
                    break;
                case 'Октябрь':
                    index = 91;
                    break;
                case 'Ноябрь':
                    index = 92;
                    break;
                case 'Декабрь':
                    index = 93;
                    break;
            }

            return res[2] + '' + index;
        } else {
            return a;
        }
    },

    "date-ru-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "date-ru-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    },
    /* числа смешанные с дефисами */
    "num-ru-pre": function ( a ) {
        var res = a.toString().match(/[0-9\.]+/);
        if( !res ) {
            return 0;
        }
        return parseFloat(a);
    },

    "num-ru-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "num-ru-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );