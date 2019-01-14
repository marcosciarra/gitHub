/**
 * Created by clickale on 19/05/17.
 */

function encodeUrl(sezione, pagina, params) {

    var url = "?sezione=" + sezione + "&pagina=" + pagina;

    if (params != undefined) {
        if (params.constructor === Array) {
            for (i = 0; i < params.length; i++) {
                params[i] = 'p' + i + '=' + btoa(params[i]);
            }

            url += "&" + params.join("&");

        } else {

            url += "&p0=" + btoa(params);
        }
    }
    return url;
}

function decodeUrl(url, keyApp) {
    var params = url.split("?");
    url = url.split(".php");

    app = new Array();
    app['home'] = url[0] +".php";
    app['baseurl'] = url[0].replace('/form/home','');
    app['form'] = url[0].replace('/home','');

    if(params[1]){

        params = params[1].split("&");

        app['sezione'] = params[0].replace('sezione=', '');
        app['pagina'] = params[1].replace('pagina=', '');

        if (keyApp != undefined) {
            if (keyApp.constructor === Array) {
                key = keyApp;
            } else {
                key = new Array();
                key[0] = keyApp;
            }
            if (params.length > 2) {

                for (i = 0; i < params.length - 2; i++) {
                    app[key[i]] = atob(params[i+2].replace('p' + i + '=', ''));
                }
            }
        }
    }
    return app;
}


function encodeUrlTest(sezione, pagina, params) {

    var url = "?sezione=" + sezione + "&pagina=" + pagina;

    if (params != undefined) {
        if (params.constructor === Array) {
            for (i = 0; i < params.length; i++) {
                params[i] = 'p' + i + '=' + btoa(params[i]);
            }

            url += "&" + params.join("&");

        } else {

            url += "&p0=" + btoa(params);
        }
    }
    return url;
}

function decodeUrlTest(url, keyApp) {

    var params = url.split("?");
    url = url.split(".php");

    app = new Array();
    app['home'] = url[0] +".php";
    app['baseurl'] = url[0].replace('/form/home','');
    app['form'] = url[0].replace('/home','');

    if(params[1]) {

        params = params[1].split("&");

        app['sezione'] = params[0].replace('sezione=', '');
        app['pagina'] = params[1].replace('pagina=', '');

        if (keyApp != undefined) {
            if (keyApp.constructor === Array) {
                key = keyApp;
            } else {
                key = new Array();
                key[0] = keyApp;
            }
            if (params.length > 2) {

                for (i = 0; i < keyApp.length -1; i++) {
                    app[key[i]] = params[i + 2].replace('p' + i + '=', '');
                }
            }
        }
    }
    return app;
}