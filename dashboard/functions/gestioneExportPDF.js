function getStrutturaBasePDF () {
    return {
        //generali
        'nomeFile': 'export',
        'foglio': 'A4',
        'orientamento': 'L',
        'tema': 'striped',
        //margini
        'margineSinistro': 12,
        'margineAlto': 12,
        'margineDestro': 12,
        'margineBasso': 12,
        //font
        'dimensioneFont': 8,
        'tipoFont': 'helvetica',
        'stileFont': 'normal',
        //celle
        'paddingCella': 1,
        'testoCella': 'linebreak',
        'larghezzaCella': 'auto',
        //header&footer
        'hf': true,
        'dimensioneFontHeader': 8,
        'dimensioneFontFooter': 8
    }
}

function getLogo (){
    return "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEgAAABGCAYAAABv59I3AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA/dpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTM4IDc5LjE1OTgyNCwgMjAxNi8wOS8xNC0wMTowOTowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1wTU06T3JpZ2luYWxEb2N1bWVudElEPSJ1dWlkOjVEMjA4OTI0OTNCRkRCMTE5MTRBODU5MEQzMTUwOEM4IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkRGODlEOEU2RTk1MzExRTc4QkI1QjE1QTg1ODY4RUI3IiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkRGODlEOEU1RTk1MzExRTc4QkI1QjE1QTg1ODY4RUI3IiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDUzQgTWFjaW50b3NoIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NzQxMTdERTUyMDA3MTE2ODhENEFFRDg5OEIzOTg1NDIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NzQxMTdGRTEy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";
}


function scaricaPDF(stampa,header,data,title) {

    //stampalog(stampalog);

    //CREO IL FILE DEFINENDO ORIENTAMENTO E DIMENSIONI FOGLIO
    if(stampa.orientamento === 'L'){
        if(stampa.foglio === 'A3'){
            var doc = new jsPDF(stampa.orientamento, 'mm', [420, 297]);
            stampa.larghezzaContenitoreDati = 420;
        }
        if(stampa.foglio === 'A4'){
            var doc = new jsPDF(stampa.orientamento, 'mm', [297, 210]);
            stampa.larghezzaContenitoreDati = 297;
        }
    }
    if(stampa.orientamento === 'P'){
        if(stampa.foglio === 'A3'){
            var doc = new jsPDF(stampa.orientamento, 'mm', [297, 420]);
            stampa.larghezzaContenitoreDati = 297;
        }
        if(stampa.foglio === 'A4'){
            var doc = new jsPDF(stampa.orientamento, 'mm', [210, 297]);
            stampa.larghezzaContenitoreDati = 210;
        }
    }

    if(stampa.hf){
        stampa.margineAlto = 30;
        var pageContent = function (data) {
            doc.setFontSize(stampa.dimensioneFontHeader);
            //posso inserire immagine (se alta XXpixel, aggiungo XXpixel a margineAlto
            doc.addImage(getLogo(), 'JPEG', stampa.margineSinistro, 5, 25, 25);

            // FOOTER
            doc.setFontSize(stampa.dimensioneFontFooter);
            var foot = "Pagina " + data.pageCount;
            doc.text(foot, data.settings.margin.left, doc.internal.pageSize.height - 5);
        };
    }else{
        stampa.margineAlto = 15;
    }

    if(title != undefined)
    {
        if(stampa.hf) {
            doc.setFontSize(10);
            doc.text(title, stampa.margineSinistro, 35);
            stampa.margineAlto = 40;
        }else{
            doc.setFontSize(10);
            doc.text(title, stampa.margineSinistro, 10);
            stampa.margineAlto = 15;
        }

    }

    doc.autoTable(header, data, {

        //aggiungo header e footer
        addPageContent: pageContent,

        theme: stampa.tema,
        pageBreak: 'avoid', // 'auto', 'avoid'
        tableWidth: stampa.larghezzaContenitoreDati - (stampa.margineSinistro + stampa.margineDestro), // 'auto', 'wrap' or a number, A4:3508*2480 A3:4134*2923

        margin:{
            left: stampa.margineSinistro,
            top: stampa.margineAlto,
            right: stampa.margineDestro,
            bottom: stampa.margineBasso
        },

        styles: {
            cellPadding: stampa.paddingCella,
            font: stampa.tipoFont,
            fontSize: stampa.dimensioneFont,
            fontStyle: stampa.stileFont,
            overflow: stampa.testoCella,
            columnWidth: 'auto'
        }

    });

    doc.save(stampa.nomeFile+'.pdf');
};