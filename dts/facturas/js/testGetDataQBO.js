
$(document).ready(function(){
	$('#getCustomers').on('click', function(){

		getCustomersQBO();

	});

	$('#getEmployees').on('click', function(){

		getEmployeesQBO();

	});

	$('#getAccounts').on('click', function(){

		getAccountsQBO();

	});

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


function getCustomersQBO(){

	console.log('getCustomersDBO');
    $.ajax({
        url: 'controller/quickbooks.php',
        data: {'method': 'getCustomersQBO' },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
        console.log(r);
		 if (r != false){
                
                var html = "<table border='1' width='95%' cellpadding='5'>";

                html += "<tr><th>No.</th><th>Name</th><th>FamilyName</th><th>EmailAddr</th><th>Phone</th><th>BillAddr</th></tr>";
                
                $.each(r, function( key, value ) {

                    html += "<tr id=''>";
                    html += "<td>" + (key+1) + "</td>";
                    html += "<td>" + value.GivenName + "</td>";
                    html += "<td>" + value.FamilyName + "</td>";
                    html += (value.PrimaryEmailAddr) ? "<td>" + value.PrimaryEmailAddr.Address + "</td>":"<td></td>";

                    html += (value.PrimaryPhone) ? "<td>" + value.PrimaryPhone.FreeFormNumber + "</td>": "<td></td>"; 
                    html += ( value.BillAddr ) ? "<td>" + value.BillAddr.City + ', ' + value.BillAddr.Country + ', ' + value.BillAddr.Line1 + ', ' + value.BillAddr.PostalCode + "</td>": "<td></td>";
                    html += "</tr>";
                    
                  });
                html += "</table>"
              $('.data').html(html);
            } else {
            	window.location = '?pag=qbo';
            }

             //	$('.data').html(r); 
            
        }
    }).fail(function(r){
        console.log(r);
            $('#msg .modal-body').html('Error consultando customer(s) QBO');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });


}



function getEmployeesQBO(){

	console.log('getEmployeesQBO');
       $.ajax({
        url: 'controller/quickbooks.php',
        data: {'method': 'getEmployeesQBO' },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
        console.log(r);
		 if (r != false){
                
                var html = "<table border='1' width='95%' cellpadding='5'>";

                html += "<tr><th>No.</th><th>Name</th><th>FamilyName</th><th>EmailAddr</th><th>Phone</th><th>BirthDate</th></tr>";
                
                $.each(r, function( key, value ) {

                    html += "<tr id=''>";
                    html += "<td>" + (key+1) + "</td>";
                    html += "<td>" + value.GivenName + "</td>";
                    html += "<td>" + value.FamilyName + "</td>";
                    html += (value.PrimaryEmailAddr) ? "<td>" + value.PrimaryEmailAddr.Address + "</td>":"<td></td>";

                    html += (value.PrimaryPhone) ? "<td>" + value.PrimaryPhone.FreeFormNumber + "</td>": "<td></td>"; 
                    html += ( value.BirthDate ) ? "<td>" + value.BirthDate + "</td>": "<td></td>";
                    html += "</tr>";
                    
                  });
                html += "</table>"
              $('.data').html(html);
            } else {
            	window.location = '?pag=qbo';
            }

             //	$('.data').html(r); 
            
        }
    }).fail(function(r){
        console.log(r);
            $('#msg .modal-body').html('Error consultando customer(s) QBO');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });



}



function getAccountsQBO(){

	console.log('getAccountsQBO');
         $.ajax({
        url: 'controller/quickbooks.php',
        data: {'method': 'getAccountsQBO' },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
        console.log(r);
		 if (r != false){
                
                var html = "<table border='1' width='95%' cellpadding='5'>";

                html += "<tr><th>No.</th><th>Name</th><th>AccountType</th><th>CurrentBalance</th><th>CurrencyRef</th><th>Active</th></tr>";
                
                $.each(r, function( key, value ) {

                    html += "<tr id=''>";
                    html += "<td>" + (key+1) + "</td>";
                    html += "<td>" + value.Name + "</td>";
                    html += "<td>" + value.AccountType + "</td>";
                    html += (value.CurrentBalance) ? "<td>" + value.CurrentBalance + "</td>":"<td></td>";

                    html += (value.CurrencyRef) ? "<td>" + value.CurrencyRef + "</td>": "<td></td>"; 
                    html += ( value.Active ) ? "<td>" + value.Active + "</td>": "<td></td>";
                    html += "</tr>";
                    
                  });
                html += "</table>"
              $('.data').html(html);
            } else {
            	window.location = '?pag=qbo';
            }

             //	$('.data').html(r); 
            
        }
    }).fail(function(r){
        console.log(r);
            $('#msg .modal-body').html('Error consultando customer(s) QBO');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });


}



function getSalesReceiptQBO(){

	console.log('getSalesReceiptQBO');
    var from = $('#fecha-from').val();
    var to = $('#fecha-to').val();
           $.ajax({
        url: 'controller/quickbooks.php',
        data: {'method': 'getSalesReceiptQBO', 'parameters': {'id':'', 'from': from, 'to' : to} },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
        console.error(r);
        $('#test').html(r);

		 if (r != false){
                
                var html = "<table border='1' width='100%' cellpadding='5'>";

                html += "<tr><th></th><th>No.</th><th>DocNumber</th><th>CustomerMemo</th><th>BillAddr</th><th>TotalAmt</th><th>TxnDate</th></tr>";
                
                $.each(r, function( key, value ) {

                    html += "<tr id='"+value.Id+"'>";
                    html += "<td><input type='radio' class='center-block' name='salesSelected' value='"+value.Id+"'/></td>";
                    html += "<td>" + (key+1) + "</td>";
                    html += "<td>" + value.DocNumber + "</td>";
                    html += "<td>" + value.CustomerMemo + "</td>";
                    html += (value.BillAddr) ? "<td>" + value.BillAddr.Line1 + ', ' + value.BillAddr.Line2 + "</td>":"<td></td>";

                    html += (value.TotalAmt) ? "<td>" + value.TotalAmt + "</td>": "<td></td>"; 
                    html += ( value.TxnDate ) ? "<td>" + value.MetaData.CreateTime + "</td>": "<td></td>";
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
            $('#msg .modal-body').html('Error consultando customer(s) QBO');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });



}


function getData(id){


        console.log('getDataSalesReceiptQBO');
    var from = $('#fecha-from').val();
    var to = $('#fecha-to').val();
           $.ajax({
        url: 'controller/quickbooks.php',
        data: {'method': 'getSalesReceiptQBO', 'parameters': {'id':id} },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
        console.error(r);
         if (r != false){
                
                var html = "<table border='1' width='100%' cellpadding='5'>";

                $.each(r, function( key, value ) {

                   // alert(value.Line);
                    console.error(value.Line);
                    $.ajax({
                        
                        url : 'createExcel.php',
                        method: 'post',
                        data: { 'lines' : value.Line },
                        success: function(resp){
                            
                            console.error(resp);
                            if (resp = true){
                                document.location = '?pag=main&pqbo';
                            }
                        }
                        
                    });
                    
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
            $('#msg .modal-body').html('Error consultando customer(s) QBO');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });



}


