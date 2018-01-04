/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function getListCompanies(id) {
    var rif = $('#emp-box').val();
    if (rif ==""){
                $('#msg .modal-body').html("Debe Ingresar el RIF");
                $('#msg').addClass('error');
                $('#msg').modal('toggle');
                return;
    }
    
    $.ajax({
        url: 'controller/companies.php',
        data: {'method': 'getCompanies', 'parameters': {'id':rif}},
        method: 'post',
        dataType: 'json',
        success: function (r) {
            console.log(r);

            if (r != false) {

                var html = "";

                $.each(r, function (key, value) {
                    $('#razon-box-txt').val(value.razon);
                    $('#direccion-box-txt').val(value.dir);
                   

                });
               // $('#emp-box').append(html);
            }
        }
    }).fail(function (r) {
        $('#msg').html('Error getting companies');
    });
}


function refreshProducts(dat) {
    
    //alert(dat);
    $.ajax({
        url: 'readExcel.php',
        type: 'POST',
        data: {'dat' : dat},
        dataType: 'json',
        success: function (r) {
            console.error(r);
            if (r != false) {

                var html = "";
                var count = 0;
                var subTotal = 0.0;
                var total = 0.0;
               // var impuesto = parseInt($('#impuesto-txt').val());
               // var descuento = parseFloat($('#descuento-txt').val());
               var impuesto =0, descuento=0.0;
         //       console.log(descuento);
           //     console.log(impuesto);
                var montoDescuento = 0.0;

                $.each(r, function (key, value) {
                    count = count + 1;
                    subTotal = (parseFloat(subTotal) + parseFloat((value.total).replace(',', '')));
             //       console.log(subTotal);
                    html += "<tr >";
                    html += "<td>" + (key + 1) + "</td>";
                    html += "<td>" + value.cant + "</td>";
                    html += "<td>" + value.numPart + "</td>";
                    html += "<td>" + value.desc + "</td>";

                    html += "<td class='"+value.prec+"'> <input class='form-control edit-edit-price' type='text' value='$"+ (value.prec).replace(new RegExp(/[^0-9.]/g),'') +"'</td>";
                    html += "<td>$" + value.total + "</td>";
                    // html += "<td><a class='edit icon'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a><a class='delete'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
                    html += "</tr>";

                });
                $('#products tbody').html(html);
                $('#details-products').val(count);
                $('#sub-total-txt').val('$'+subTotal.formatMoney(2, '.', ','));

                calculateTotal();
            }
        }


    }).fail(function(r){
        
        console.error(r);
    });

}


function _GET(param)
{
/* Obtener la url completa */
url = document.URL;
/* Buscar a partir del signo de interrogación ? */
url = String(url.match(/\?+.+/));
/* limpiar la cadena quitándole el signo ? */
url = url.replace("?", "");
/* Crear un array con parametro=valor */
url = url.split("&");

/* 
Recorrer el array url
obtener el valor y dividirlo en dos partes a través del signo = 
0 = parametro
1 = valor
Si el parámetro existe devolver su valor
*/
x = 0;
while (x < url.length)
{
p = url[x].split("=");
if (p[0] == param)
{
return decodeURIComponent(p[1]);
}
x++;
}
}



Number.prototype.formatMoney = function (c, d, t) {
    var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

/**
 * Comment
 */
function loadFile(event) {
    var files = event.target.files;

    // Create a formdata object and add the files
    var data = new FormData();
    $.each(files, function (key, value)
    {
        data.append(key, value);
    });
    $.ajax({
        url: 'uploadFile.php?files',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function (data, textStatus, jqXHR)
        {
            if (typeof data.error === 'undefined')
            {
                // Success so call function to process the form
                $('#infile').filestyle('clear');
                refreshProducts('p');
            }
            else
            {
                // Handle errors here
                console.log('ERRORS: ' + data.error);
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            // Handle errors here
            console.log('ERRORS: ' + textStatus);
            // STOP LOADING SPINNER
        }
    });

}


function putPedido() {
console.log($('#emp-box').val());
    if ($('#emp-box').val() == '') {
        $('#msg .modal-body').html('debe seleccionar la empresa');
        $('#msg').addClass('error');
        $('#msg').modal('toggle');
        $('#emp-box').focus();


    } else {
        var pedido = new Object();
        pedido.num_invoice = $('#invoice-box').val();
        pedido.num_orden = $('#order-box').val();
        pedido.fecha_emision = $('#fecha-box').val();
        pedido.cliente_rif = $('#rif-txt').val();
        pedido.empresa_rif = $('#emp-box').val();
        pedido.sub_total = $('#sub-total-txt').val();
        pedido.sale_tax = 0;
        pedido.discount = 0;
        pedido.freight = $('#entrega-txt').val().replace(new RegExp(/[^0-9.]/g),'');
        pedido.handling = 0;
        pedido.restocking = $('#repo-txt').val().replace(new RegExp(/[^0-9.]/g),'');
        pedido.total_sale = $('#total-txt').val();
        pedido.detalle = [];
        var detalle = new Object();

        $('#products tbody tr').each(function (k, v) {

            detalle.num_invoice = $('#invoice-box').val(),
                    detalle.qty = $(v).find("td").eq(1).html(),
                    detalle.numPart = $(v).find("td").eq(2).html(),
                    detalle.descripcion = $(v).find("td").eq(3).html(),
                    detalle.precio_unit = $(v).find("td").eq(4).find('input').val(),
                    detalle.sub_total = $(v).find("td").eq(5).html();

            (pedido.detalle).push(detalle);
            detalle = new Object();
        });

        var piePag = new Object();
        
        piePag.pie = $('#pie-txt').val();
        piePag.contactName = $('#contactName-txt').val();
        piePag.tlfLocal = $('#tlfLocal-txt').val();
        piePag.tlfWork = $('#tlfWork-txt').val();
        piePag.email = $('#email-txt').val();
        piePag.via = $('#via-txt').val();
        piePag.terms = $('#terms-txt').val();
        piePag.incomeTerms = $('#incomeTerms-txt').val();
        
        
        piePag = JSON.stringify(piePag);

        ///console.log(pedido);

        $.ajax({
            url: 'controller/pedidos.php',
            data: {'method': 'putPedido', 'parameters': {'pedido': pedido}},
            method: 'post',
            dataType: 'json',
            success: function (r) {
                console.log(r);

                if (!('error' in r)) {
                    console.log(r.result);
                    $('#msg .modal-body').html((r.result).split(';')[0]);
                    $('#msg').addClass('success');
                    $('#msg').modal('toggle');
                   
                    var f = $('#fact-type-box').val();
                    var i = (r.result).split(';')[1];
                    console.log('f: '+f + ' i: ' + i);
                    window.open('reportes?f=' +f+ '&i='+i+'&p='+piePag , '_blank');
                    //return false;
                     $('#main-form')[0].reset();
                     $('#products tbody tr').remove();

                } else {
                    $('#msg .modal-body').html(r.error);
                    $('#msg').addClass('error');
                    $('#msg').modal('toggle');
                }
            }
        }).fail(function (r, msg) {
            $('#msg .modal-body').html('Error creating Pedido');
            console.log('error: ' + msg);
        });
    }
}



/**
 * Comment
 */
function findCustomer() {

    var rif = $('#rif-txt').val();
    var cliente = new Object();
    console.log('rif: ' + rif);
    cliente.rif = rif;

    $.ajax({
        url: 'controller/crud.php',
        data: {'functionname': 'buscar_cliente', 'arguments': cliente},
        method: 'post',
        dataType: 'json',
        success: function (r) {
            console.log(r);


            var html = "";

            if (!('error' in r)) {

            $.each(r.result, function( key, value ) {
                $('#nomb-cliente').val(value.RAZON_SOCIAL);
                $('#tlf-cliente').val(value.TELEFONO);
                $('#fax-cliente').val('');
                $('#dir-cliente').val(value.DIRECCION);

            });
            
            } else {
                $('#msg .modal-body').html(r.error);
                $('#msg').addClass('error');
                $('#msg').modal('toggle');
            }

        }
    }).fail(function (r) {
        console.log('Error find customer:' + r);
        $('#msg').html('Error getting companies');
    });

}


function calculateTotal(){
    
    

    //var impuesto = parseInt($('#impuesto-txt').val());
    //var descuento = parseFloat($('#descuento-txt').val().replace(',',''));
    //var carga = parseFloat($('#carga-txt').val().replace(',',''));
    var entrega = (($('#entrega-txt').val().replace(',','')).replace(new RegExp(/[^0-9.]/g), ''));
    var insurance = (($('#repo-txt').val().replace(',','')).replace(new RegExp(/[^0-9.]/g), ''));

    
    console.log(entrega);
    console.log(insurance);

     if (entrega == ""){
         entrega = 0.0;
        
     } else {
        entrega = parseFloat(entrega);
     }

     if (insurance == ""){
         insurance = 0.0;
        
     } else {
        insurance = parseFloat(insurance);
     }
    var descuento = 0.0, carga=0.0;
    var impuesto = 0;
    var montoDescuento = 0.0, final = 0.0;
    var subTotal =parseFloat(($('#sub-total-txt').val().replace(',','')).replace(new RegExp(/[^0-9.]/g), ''));
    montoDescuento = parseFloat(subTotal - descuento);
    final = montoDescuento + carga + entrega + insurance
    $('#total-txt').val('$'+(final + parseFloat((final * impuesto) / 100)).formatMoney(2, '.', ','));

}


function aplicateDiscount(){
    
    
    var porcentaje = parseInt(($('#porc-desc-txt').val().replace(',','')).replace(new RegExp(/[^0-9.]/g), ''));
    var subTotal = 0.0;
    var valor;
    var valorFinal;
   // var impuesto = parseInt($('#impuesto-txt').val());
   // var descuento = parseFloat($('#descuento-txt').val());
   var impuesto = 0;
   var descuento = 0;
    
    var montoDescuento = 0.0;
    var cantidad=0;
    var producto=0.0;
  //  console.log('aplicate');

     $('#products tbody tr').each(function (key, value) {
                    //count = count + 1;
                    
                    //console.log(subTotal);
                    //console.log($(this).children('td').eq(4).text() + "----");
                    cantidad = ($(this).children('td').eq(1).text()).replace(new RegExp(/[^0-9.]/g), '');
                    producto = parseFloat((($(this).children('td').eq(4).attr('class')).replace(',','')).replace(new RegExp(/[^0-9.]/g), ''));
                    valor = cantidad * producto;
                    //valor = valor.replace(',','');
                    //valor = valor.replace(/[^0-9.]/, '');
                    //console.log(valor);
                    
                        if (porcentaje > 0){
                            if ($('#descuento').is(':checked')){
                                //valorFinal = valor - (parseFloat(valor) * porcentaje/100);
                                producto = producto - (parseFloat(producto) * porcentaje/100);
                            } else {
                                //valorFinal = valor + (parseFloat(valor) * porcentaje/100);
                                producto = producto + (parseFloat(producto) * porcentaje/100);
                            }
                   valorFinal = cantidad * producto;
                    
                } else {
                    valorFinal = cantidad * producto;
                }
                    
                subTotal = (parseFloat(subTotal) + parseFloat((valorFinal)));
                
         
                   // $('#products tbody').html(html);
                  //  $('#details-products').val(count);


                   $(this).children('td').eq(4).find('input').val('$'+parseFloat(producto).formatMoney(2, '.', ','));
                   $(this).children('td').eq(5).text('$'+valorFinal.formatMoney(2, '.', ','));

    });
    
                    $('#sub-total-txt').val('$'+subTotal.formatMoney(2, '.', ','));
                    calculateTotal();
                   // montoDescuento = parseFloat(subTotal - descuento);
                  //  console.log(montoDescuento);
                   // $('#total-txt').val((montoDescuento + parseFloat((montoDescuento * impuesto) / 100)).formatMoney(2, '.', ','));
                
}


function calculateSubtotal(){

 
    var porcentaje = $('#porc-desc-txt').val();
    var subTotal = 0.0;
    var valor;
    var valorFinal;
   // var impuesto = parseInt($('#impuesto-txt').val());
   // var descuento = parseFloat($('#descuento-txt').val());
   var impuesto = 0, descuento = 0.0;
    
    var montoDescuento = 0.0;
    var cantidad=0;
    var producto=0.0;
  //  console.log('aplicate');

     $('#products tbody tr').each(function (key, value) {
                    //count = count + 1;
                    
                    //console.log(subTotal);
                    //console.log($(this).children('td').eq(4).text() + "----");
                    cantidad = parseInt(($(this).children('td').eq(1).text()).replace(new RegExp(/[^0-9.]/g), ''));
                    producto = parseFloat((($(this).children('td').eq(4).find('input').val()).replace(',','')).replace(new RegExp(/[^0-9.]/g), ''));
                    valor = cantidad * producto;
                    //valor = valor.replace(',','');
                    //valor = valor.replace(/[^0-9.]/, '');
                    //console.log(valor);
                      valorFinal= (($(this).children('td').eq(5).text()).replace(',','')).replace(new RegExp(/[^0-9.]/g), '');
                subTotal = (parseFloat(subTotal) + parseFloat((valorFinal)));
                
         
                   // $('#products tbody').html(html);
                  //  $('#details-products').val(count);
                //   $(this).children('td').eq(4).find('input').val(parseFloat(producto).formatMoney(2, '.', ','));
                 //  $(this).children('td').eq(5).text(valorFinal.formatMoney(2, '.', ','));

    });
    
                    $('#sub-total-txt').val('$'+subTotal.formatMoney(2, '.', ','));
                  //  montoDescuento = parseFloat(subTotal - descuento);
                  //  console.log(montoDescuento);
                 //   $('#total-txt').val((montoDescuento + parseFloat((montoDescuento * impuesto) / 100)).formatMoney(2, '.', ','));
                



}


$(document).ready(function () {

//$(":file").filestyle();

//var editActivated = false;

    var dat = 0;
    dat = _GET('pqbo');    
    //console.error(dat);
    
    if (dat == 1){
       //alert('entra');
       // $('#infile').attr('disabled');
        refreshProducts('pqbo');
        $('#exportar-file').css("display", "block");
    }


    $('body').on('click', '.edit', function () {
        editCompany(this).parent().parent().attr('id');
    });

    $('body').on('click', '#find-custumer', function () {
        findCustomer();
    });

    $('body').on('click', '#find-company', function () {
       getListCompanies();
    });
    

    $('body').on('dblclick','#products tbody tr td:nth-child(5)',function (){
           
        //   if (!editActivated){
         //  var texto = $(this).text();
         //  var input = "<input type='text' class='txt-edit-price' value='"+texto+"'/>";
//input = '<div class="input-group input-group-sm"> <span class="input-group-btn"> <button class="btn btn-default" id="ok-edit-txt" type="button">Ok</button> </span> <input id="txt-edit-price" type="text" class="form-control" value="'+texto+'"> </div>';

       //    $(this).html(input);
        //   editActivated = true;
     //  }
             
           //$(this).text($(this).find('.txt-edit-price').val());
           //$(this).find('.txt-edit-price').val('');
           //$(this).find('.txt-edit-price').hide('slow');
          

    });

    $('body').on('click','#ok-edit-txt',function (){

    //  $(this).parent().parent().parent('td').text($('#txt-edit-price').val());
    //  editActivated = false;

     });

    $('#infile').change(loadFile);

    
    $('#porc-desc-txt, #descuento, #aumento').change(function (){
        aplicateDiscount();
        
    });


    $('#fecha-box').datepicker({dateFormat: 'dd/mm/yy',
        beforeShow:function(input) {
        $('#fecha-box').css({
            "position": "relative",
            "z-index": 999999
        });
    }

    });

    $('#generar-pedido').on('click', function () {
        putPedido();
    });
    
    
    $('#impuesto-txt').change(calculateTotal);
    $('#descuento-txt, #repo-txt, #carga-txt, #entrega-txt, #impuesto-txt').keyup(calculateTotal);

    $('body').on('keyup', '#products tbody tr .edit-edit-price',function(){
       console.log('recalcule-price-edit');
       var producto = parseFloat(($(this).val()).replace(',','').replace(new RegExp(/[^0-9.]/g), ''));
       console.log(producto);
       if (($(this).val()).replace(',','').replace(new RegExp(/[^0-9.]/g), '') == ""){
    producto =0.0;
}

       var cantidad = parseInt(($(this).parent().siblings().eq(1).text()).replace(new RegExp(/[^0-9.]/g), ''));
       console.log(cantidad);

       var final = (cantidad * producto);
       console.log(final);

       $(this).parent().siblings().eq(4).text('$'+(final).formatMoney(2, '.', ','));
      calculateSubtotal();
      calculateTotal()

   });

    $('#fecha-box').css('z-index', '9999');

});