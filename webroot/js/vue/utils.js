/*
* valore in 1000.50
*/
function numberToJs(number) {
    if(number==undefined) return '0.00';

    /* elimino le migliaia */
    number = number.replace('.','');

    /* converto eventuali decimanali */
    number = number.replace(',','.');

    return number;
}


/*
  da 1000.5678 in 1.000,57
  da 1000 in 1.000,00
*/
function numberFormat(number, decimals, dec_point, thousands_sep) {

    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    var d = dec_point == undefined ? "." : dec_point;
    var t = thousands_sep == undefined ? "," : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function getUmRifValues(um, prezzo_um_riferimento) {
    let um_rif_values = [];
    let um_rif_values_prezzo = 0;

    if (um == 'PZ') {
        um_rif_values_prezzo = prezzo_um_riferimento;
        um_rif_values.push({id: 'PZ', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;'});
    } else
    if (um == 'KG') {
        um_rif_values_prezzo = (prezzo_um_riferimento / 1000);
        um_rif_values.push({id: 'GR', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo'});

        um_rif_values_prezzo = (prezzo_um_riferimento / 10);
        um_rif_values.push({id: 'HG', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo'});

        um_rif_values_prezzo = (prezzo_um_riferimento);
        um_rif_values.push({id: 'KG', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo'});
    } else
    if (um == 'HG') {
        um_rif_values_prezzo = (prezzo_um_riferimento / 100);
        um_rif_values.push({id: 'GR', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo'});

        um_rif_values_prezzo = (prezzo_um_riferimento);
        um_rif_values.push({id: 'HG', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo'});

        um_rif_values_prezzo = (prezzo_um_riferimento * 10);
        um_rif_values.push({id: 'KG', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo'});
    } else
    if (um == 'GR') {
        um_rif_values_prezzo = (prezzo_um_riferimento);
        um_rif_values.push({id: 'GR', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Grammo'});

        um_rif_values_prezzo = (prezzo_um_riferimento * 100);
        um_rif_values.push({id: 'HG', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Ettogrammo'});

        um_rif_values_prezzo = (prezzo_um_riferimento * 1000);
        um_rif_values.push({id: 'KG', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Chilo'});
    } else
    if (um == 'LT') {
        um_rif_values_prezzo = (prezzo_um_riferimento / 1000);
        um_rif_values.push({id: 'ML', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro'});

        um_rif_values_prezzo = (prezzo_um_riferimento / 10);
        um_rif_values.push({id: 'DL', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro'});

        um_rif_values_prezzo = (prezzo_um_riferimento);
        um_rif_values.push({id: 'LT', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro'});
    } else
    if (um == 'DL') {
        um_rif_values_prezzo = (prezzo_um_riferimento / 100);
        um_rif_values.push({id: 'ML', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro'});

        um_rif_values_prezzo = (prezzo_um_riferimento);
        um_rif_values.push({id: 'DL', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro'});

        um_rif_values_prezzo = (prezzo_um_riferimento * 10);
        um_rif_values.push({id: 'LT', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro'});
    } else
    if (um == 'ML') {
        um_rif_values_prezzo = (prezzo_um_riferimento);
        um_rif_values.push({id: 'ML', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Millilitro'});

        um_rif_values_prezzo = (prezzo_um_riferimento * 100);
        um_rif_values.push({id: 'DL', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Decilitro'});

        um_rif_values_prezzo = (prezzo_um_riferimento * 1000);
        um_rif_values.push({id: 'LT', value: numberFormat(um_rif_values_prezzo, 2, ',', '.') + '&nbsp;&euro;&nbsp;al&nbsp;Litro'});
    }

    return um_rif_values;
}

