/**
 * Created by alessandro on 01/08/17.
 */


function formatStringToDate(string) {

    if(string !== undefined){
        if(string.length === 8){
            return string.substring(6,8) + "/" +string.substring(4,6) + "/" + string.substring(0,4);
        }
        else if(string.length === 6){
            return string.substring(4,6) + "/" +string.substring(2,4) + "/" + string.substring(0,2);
        }
        else{
            return string;
        }
    }else{
        return '';
    }
}

function formatStringToTime(string) {

    if(string !== undefined){
        if(string.length === 6){
            return string.substring(0,2) + ":" +string.substring(2,4) + ":" + string.substring(4,6);
        }
        else{
            return string;
        }
    }else{
        return '';
    }
}

//trasforma la data che passo in stringa formato YYYYmmdd
Date.prototype.yyyymmdd = function() {
    var mm = this.getMonth() + 1; // getMonth() is zero-based
    var dd = this.getDate();

    return [this.getFullYear(),
        (mm>9 ? '' : '0') + mm,
        (dd>9 ? '' : '0') + dd
    ].join('');
};


function getTimeStampDateNow () {

    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    var hh = today.getHours();
    var ii = today.getMinutes();
    var ss = today.getMilliseconds();

    if(dd<10) {
        dd = '0'+dd
    }

    if(mm<10) {
        mm = '0'+mm
    }

    if(hh<10){
        hh = '0'+hh
    }

    if(ii<10){
        ii = '0'+ii
    }

    if(ss<10){
        ss = '00'+ss
    }else if(ss<100){
        ss = '0'+ss
    }

    return yyyy+mm+dd+hh+ii+ss;
}