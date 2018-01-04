

function deleteCliente(id){
    var args = {'rif' : id};
    jQuery.ajax({
			type: "POST",
			url: 'controller/crud.php',
			dataType: 'json',
			data: {functionname: 'eliminar_cliente', arguments:args},
			success: function (obj, textstatus){
                            console.log(obj);
				if(!('error' in obj)){															
					console.log(obj.result);
                                        $('#msg .modal-body').html(obj.result);
                                        $('#msg').addClass('success');
					$('#msg').modal('toggle');
                                        
				}else{
                                    //console.log(id.lenght);
                                    
					console.log(obj.error);
					$('#msg .modal-body').html(obj.error);
					$('#msg').addClass('error');
					$('#msg').modal('toggle');
                                        
                                
					/*$('#msg').html(obj.error);
					$('#mesaje').modal();*/
				}
                                $('#consultar').click();
			}
		}).fail(function(r){
                    console.log(r);
                });	
    
    
}


$( document ).ready(function() {
    console.log( "ready!" );	
	$('#guardar_edit').hide();
	$('#rif').keyup(function(){
		var x = document.getElementById("rif");							
		x.value = x.value.toUpperCase();		
	});		
	
	$('#guardar').on('click',function(){
            
		var rif = $('#rif').val();
		var razon_social = $('#razon_social').val();
		var dir = $('#dir').val();
		var telefono = $('#telefono').val();
		var args = {'rif' : rif,'razon_social' : razon_social, 'direccion': dir, 'telefono':telefono};
		console.log(args);
		jQuery.ajax({
			type: "POST",
			url: 'controller/crud.php',
			dataType: 'json',
			data: {functionname: 'agregar_cliente', arguments:args},
			success: function (obj, textstatus){
                            console.log(obj);
				if(!('error' in obj)){															
					console.log(obj.result);
                                        $('#msg .modal-body').html(obj.result);
                                        $('#msg').addClass('success');
					$('#msg').modal('toggle');
                                        $('#consultar').click();
				}else{
					console.log(obj.error);
					$('#msg .modal-body').html(obj.error);
					$('#msg').addClass('error');
					$('#msg').modal('toggle');
					/*$('#msg').html(obj.error);
					$('#mesaje').modal();*/
				}
			}
		}).fail(function(r){
                    console.log(r);
                });							
	});
	
	$('#guardar_edit').on('click',function(){

            if (!$(this).is(':visible')){
                alert('no');
                return;
            }
		var rif = $('#rif').val();
		var razon_social = $('#razon_social').val();
		var dir = $('#dir').val();
		var telefono = $('#telefono').val();
		var args = {'rif' : rif,'razon_social' : razon_social, 'direccion': dir, 'telefono':telefono};
		console.log(args);
		jQuery.ajax({
			type: "POST",
			url: 'controller/crud.php',
			dataType: 'json',
			data: {functionname: 'editar_cliente', arguments:args},
			success: function (obj, textstatus){
				if(!('error' in obj)){															
					console.log(obj.result);
					
                                        $('#msg .modal-body').html(obj.result);
                                        $('#msg').addClass('success');
					$('#msg').modal('toggle');
                                        $('#form-cliente')[0].reset();
                                        $('#guardar_edit').hide('slow');
                                        $('#guardar').show('slow');
                                        $('#rif').prop('disabled', false);
                                        $('#consultar').click();
				}else{
					console.log(obj.error);
					console.log(obj.query);
					$('#msg .modal-body').html(obj.error);
					$('#msg').addClass('error');
					$('#msg').modal();					
				}
			}
		}).fail(function(r){
                    console.log(r);
                });							
	});
	
	/*$('#guardar').on('click',function(){
		var rif = $('#rif').val();
		var razon_social = $('#razon_social').val();
		var dir = $('#dir').val();
		var telefono = $('#telefono').val();
		var args = {'rif' : rif,'razon_social' : razon_social, 'direccion': dir, 'telefono':telefono}
		console.log(args);
		jQuery.ajax({
			type: "POST",
			url: 'controller/crud.php',
			dataType: 'json',
			data: {functionname: 'agregar_cliente', arguments:args},
			success: function (obj, textstatus){
				if(!('error' in obj)){															
					console.log(obj.result);
					console.log(obj.query);
				}else{
					console.log(obj.error);
					console.log(obj.query);
					$('#msg .modal-body').html(obj.error);
					$('#msg').addClass('error');
					$('#msg').modal();					
				}
			}
		});							
	});*/
	
	$('body').on('click','.edit-cus', function(){
		console.log('Consultar todos los clientes');
		//var rif = $('#rif').val();
                var rif = $(this).parent().parent().attr('id');
		if(rif==0){
			$('#msg .modal-body').html("Debe ingresar el Rif de la Empresa a Consultar");
			$('#msg').addClass('error');
			$('#msg').modal();			
		}else{
			var args = {'rif' : rif};			
			jQuery.ajax({
				type: "POST",
				url: 'controller/crud.php',
				dataType: 'json',
				data: {functionname: 'buscar_cliente', arguments:args},			
				success: function (obj, textstatus){
					if(!('error' in obj)){							
						console.log(obj.result);
						//console.log(obj.query);
                                                 $.each(obj.result, function( key, value ) {
                                                     
						$('#rif').val(value.rif);
						$("#razon_social").val(value.RAZON_SOCIAL);
						$('#dir').val(value.DIRECCION);
						$('#telefono').val(value.TELEFONO); 
						$('#guardar_edit').show();
						$('#guardar').hide();
						$('#rif').prop('disabled', true);
                                                
                                                 });
                                                  
						/*$('#razon_social').val = "HOLA";*/
					}else{
						console.log(obj.error);	
						console.log(obj.query);
						$('#msg .modal-body').html(obj.error);
						$('#msg').addClass('error');
						$('#msg').modal();
					}
				}
			});				
		}				
	});
        
        
        /****** consultar ****/
        
        
        $('#consultar').on('click', function(){
		console.log('Consultar todos los clientes');
		var rif = $('#cus-find').val();
                //var rif = $(this).parent().parent().attr('id');
                if (rif == 0){
                    rif=null;
                }
		
			var args = {'rif' : rif};			
			jQuery.ajax({
				type: "POST",
				url: 'controller/crud.php',
                                dataType: 'json',
				data: {functionname: 'buscar_cliente', arguments:args},			
				success: function (obj, textstatus){
                                    console.log(obj);
					if(!('error' in obj)){							
						console.log(obj.result);
						
						/*$('#rif').val(obj.result['rif']);
						$("#razon_social").val(obj.result['RAZON_SOCIAL']);
						$('#dir').val(obj.result['DIRECCION']);
						$('#telefono').val(obj.result['TELEFONO']); 
						$('#guardar_edit').show();
						$('#guardar').hide();
						$('#rif').prop('disabled', true);
                                                */
                                                
                                                /******************************/
                                                var html="";
                                                var count=1;
                                                $.each(obj.result, function( key, value ) {
                                                html += "<tr id='"+value.rif+"'>";
                                                html += "<td>" + (count) + "</td>";
                                                html += "<td>" + value.rif + "</td>";
                                                html += "<td>" + value.RAZON_SOCIAL + "</td>";
                                                html += "<td>" + value.DIRECCION + "</td>";
                                                html += "<td>" + value.TELEFONO + "</td>";
                                                html += "<td><a class='edit-cus icon'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a><a class='delete-cus'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
                                                html += "</tr>";
                                                count++;
                                              });
                                              $('#customers tbody').html(html);

                                                
                                                /********************************/
                                                
                                                
						/*$('#razon_social').val = "HOLA";*/
					}else{
                                            
                                            if (typeof(id) !== "undefined"){
						console.log(obj.error);	
						console.log(obj.query);
						$('#msg .modal-body').html(obj.error);
						$('#msg').addClass('error');
						$('#msg').modal();
                                            }
                                            $('#customers tbody').html("");
					}
				}
			}).fail(function(r){
                            console.log(r);
                        });				
						
	});
        
        
        $('body').on('click', '#btn-cancelar', function(){
            $('#form-cliente')[0].reset();
            $('#guardar_edit').hide('slow');
            $('#guardar').show('slow');
            $('#rif').prop('disabled', false);
        });
        
        $('#consultar').click();
        
        $('body').on('click', '.delete-cus', function(){
            deleteCliente($(this).parent().parent().attr('id'));
        });
        
        
        
        
        
        
});

