
$(document).ready(function(){
	$('#getSales').on('click', function(){

		getSalesReceiptQBO();

	});

        $('#getData').on('click', function(){

        if ($('input[name="salesSelected"]').is(':checked')){
            getData($('input[name="salesSelected"]:checked').val());
        }
        

    });



        $('#fecha-from').datepicker({dateFormat: 'yy-mm-dd',
        beforeShow:function(input) {
            $('#fecha-from').css({
                "position": "relative",
                "z-index": 999999
            });
        }, 
        onClose: function (selectedDate) {
            $("#fecha-to").datepicker("option", "minDate", selectedDate);
        }

    });


    $('#fecha-to').datepicker({dateFormat: 'yy-mm-dd',
        beforeShow:function(input) {
            $('#fecha-to').css({
                "position": "relative",
                "z-index": 999999
            });
        },
        onClose: function (selectedDate) {
            $("#fecha-from").datepicker("option", "maxDate", selectedDate);
        }

    });



});


function getSalesReceiptQBO(){

	console.log('getSalesReceiptPRO');
    var from = $('#fecha-from').val();
    var to = $('#fecha-to').val();
           $.ajax({
        url: 'controller/profit.php',
        data: {'method': 'getSalesReceipt', 'parameters': {'id':'', 'from': from, 'to' : to} },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
        console.error(r);
		 if (r != false){
                
                var html = "<table border='1' width='100%' cellpadding='5'>";

                html += "<tr><th></th><th>No.</th><th>FacNumber</th><th>Nombre</th><th>Descrip</th><th>Total</th><th>FechaEmis</th></tr>";
                
                $.each(r, function( key, value ) {

                    html += "<tr id='"+value.Id+"'>";
                    html += "<td><input type='radio' class='center-block' name='salesSelected' value='"+value.fact_num+"'/></td>";
                    html += "<td>" + (key+1) + "</td>";
                    html += "<td>" + value.fact_num + "</td>";
                    html += "<td>" + value.nombre + "</td>";
                    html += (value.descrip) ? "<td>" + value.descrip + "</td>":"<td></td>";

                    html += (value.tot_neto) ? "<td>" + value.tot_neto + "</td>": "<td></td>"; 
                    html += ( value.fec_emis ) ? "<td>" + (value.fec_emis.date).substring(0,10) + "</td>": "<td></td>";
                    html += "</tr>";
                    
                  });
                html += "</table>"
              $('.data').html(html);
            } else {
            	//window.location = '?pag=main';
                $('#msg .modal-body').html('No se obtuvieron resultados para el rango indicado.');
                $('#msg').addClass('success');
                $('#msg').modal('toggle');

            }

             //	$('.data').html(r); 
            
        }
    }).fail(function(r){
        console.error(r);
            $('#msg .modal-body').html('Error consultando facturas');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });



}


function getData(id){


        console.log('getDetail');
    var from = $('#fecha-from').val();
    var to = $('#fecha-to').val();
           $.ajax({
        url: 'controller/profit.php',
        data: {'method': 'getDetail', 'parameters': {'id':id} },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
        console.error(r);
         if (r != false){
                
                var html = "<table border='1' width='100%' cellpadding='5'>";
       // alert(value.Line);
                   // console.error();
                    $.ajax({
                        
                        url : 'createExcelPRO.php',
                        method: 'post',
                        data: { 'lines' : r },
                        success: function(resp){
                            
                            console.error(resp);
                            if (resp = true){
                                document.location = '?pag=main&pqbo=1';
                            }
                        }
                        
                    });
                    
            
             
            } else {
                //window.location = '?pag=main';
                $('#msg .modal-body').html('No se obtuvieron resultados para el rango indicado.');
                $('#msg').addClass('success');
                $('#msg').modal('toggle');

            }

             // $('.data').html(r); 
            
        }
    }).fail(function(r){
        console.error(r);
            $('#msg .modal-body').html('Error tomando los datos');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });



}



