/**
 * Created by alessandro on 01/08/17.
 */


function formatStringToDate(string) {

    if (string !== undefined) {
        if (string.length === 8) {
            return string.substring(6, 8) + "/" + string.substring(4, 6) + "/" + string.substring(0, 4);
        }
        else if (string.length === 6) {
            return string.substring(4, 6) + "/" + string.substring(2, 4) + "/" + string.substring(0, 2);
        }
        else {
            return string;
        }
    } else {
        return '';
    }
}

function formatStringToTime(string) {

    if (string !== undefined) {
        if (string.length === 6) {
            return string.substring(0, 2) + ":" + string.substring(2, 4) + ":" + string.substring(4, 6);
        }
        else {
            return string;
        }
    } else {
        return '';
    }
}

function formatDataDbToIta(data) {

    if (data !== undefined) {
        if (data.length === 10) {
            return data.substr(8, 2) + "/" + data.substr(5, 2) + "/" + data.substr(0, 4);
        }
        else {
            return data;
        }
    } else {
        return '';
    }
}

function formatTimeToDbFormat(data) {

    if (data !== undefined) {
        if (data.length === 6) {
            return string.substring(0, 2) + ":" + string.substring(2, 4) + ":" + string.substring(4, 6);
        }
        else {
            return string;
        }
    } else {
        return '';
    }
}

//trasforma la data che passo in stringa formato YYYYmmdd
Date.prototype.yyyymmdd = function () {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();

    return [this.getFullYear(),
        (mm > 9 ? '' : '0') + mm,
        (dd > 9 ? '' : '0') + dd
    ].join('');
};


function getTimeStampDateNow() {

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    var hh = today.getHours();
    var ii = today.getMinutes();
    var ss = today.getMilliseconds();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    if (hh < 10) {
        hh = '0' + hh
    }

    if (ii < 10) {
        ii = '0' + ii
    }

    if (ss < 10) {
        ss = '00' + ss
    } else if (ss < 100) {
        ss = '0' + ss
    }

    return yyyy + mm + dd + hh + ii + ss;
}


function today() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //Gennaio = 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    return '' + yyyy + '-' + mm + '-' + dd;
}


function formattaDataHtml5(date) {
    //Mon Jan 01 2018 00:00:00 GMT+0100 (CET)
    var ret = [];
    var tmp = String(date);
    ret['day'] = tmp.substr(8, 2);
    ret['month'] = getNumberOfMonth(tmp.substr(4, 3));
    ret['year'] = tmp.substr(11, 4);
    return ret;
}


function getNumberOfMonth(mese) {
    switch (mese) {
        case "Jan":
            m = "01";
            break;
        case "Feb":
            m = "02";
            break;
        case "Mar":
            m = "03";
            break;
        case "Apr":
            m = "04";
            break;
        case "May":
            m = "05";
            break;
        case "Jun":
            m = "06";
            break;
        case "Jul":
            m = "07";
            break;
        case "Aug":
            m = "08";
            break;
        case "Sep":
            m = "09";
            break;
        case "Oct":
            m = "10";
            break;
        case "Nov":
            m = "11";
            break;
        case "Dec":
            m = "12";
            break;
    }
    return m;
}


function getJsDateFromYYYYMMGG(date){
    var data = new Date();
    var YYYY=date.substr(0,4);
    var MM=date.substr(5,2)-1;
    var DD=date.substr(-2);
    data.setFullYear(YYYY,MM,DD);

    return data;
}


function getYYYYMMGGFromJsDate(date){
    if(date==null){return null;}
    var app=new Date(date);
    var dd = app.getDate();
    var mm = app.getMonth() + 1;
    var yyyy = app.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    return yyyy + '-' + mm + '-' + dd;
}


formattaDataDbToIta = function (date) {
    var today = new Date(date);
    var dd = '00' + today.getDate();
    var mm = '00' + (today.getMonth() + 1);
    var yyyy = today.getFullYear();
    return dd.substr(-2) + '/' + mm.substr(-2) + '/' + yyyy;
};


/*
Controlla che la data passata come YYYY-MM-DD sia una data valida
 */
function isValidDate(dateString)
{
    if(dateString == null){return false;}
    // First check for the pattern
    // if(!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
    //     return false;

    // Parse the date parts to integers
    var parts = dateString.split("-");
    var day = parseInt(parts[2], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[0], 10);

    // Check the ranges of month and year
    if(year < 1000 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
};