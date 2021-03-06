function number_format(n) {
    var reg = /(^-?\d+)(\d{3})/;

    n = ('' + n).trim().replace(/[^0-9.-]/g, '').replace(/(.)-/g, '$1');
    while (reg.test(n)) {
        n = n.replace(reg, '$1,$2');
    }

    return n;
}
