function controllaPIVA(piva) {
    if (piva == '')
        return false;

    if (piva.length != 11)
        return false;

    validi = "0123456789";
    for (i = 0; i < 11; i++) {
        if (validi.indexOf(piva.charAt(i)) == -1)
            return false;
    }

    s = 0;
    for (i = 0; i <= 9; i += 2)
        s += piva.charCodeAt(i) - '0'.charCodeAt(0);
    for (i = 1; i <= 9; i += 2) {
        c = 2 * (piva.charCodeAt(i) - '0'.charCodeAt(0));
        if (c > 9) c = c - 9;
        s += c;
    }

    if ((10 - s % 10) % 10 != piva.charCodeAt(10) - '0'.charCodeAt(0))
        return false;

    return true;
}

function controllaPIVA11(value) {
    var REGEX_PIVA = new RegExp("[0-9]{11}");
    return (REGEX_PIVA.test(value) && value.length === 11);
}

function controllaPIVA12(value) {
    var REGEX_PIVA = new RegExp("[0-9]{12}");
    return (REGEX_PIVA.test(value) && value.length === 12);
}

function controllaPIVA16(value) {
    var REGEX_PIVA = new RegExp("[A-Za-z0-9]{16}");
    return (REGEX_PIVA.test(value) && value.length === 16);
}