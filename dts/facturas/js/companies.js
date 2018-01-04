/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var errorLogo = true;
var logoext;
var logoType;
function getCompanies(id) {
    console.log('getCompanies');
    $.ajax({
        url: 'controller/companies.php',
        data: {'parameters' : {'parameters':id}, 'method': 'getCompanies' },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
       console.log(r);
            if (r != false){
                
                var html = "";
                
                $.each(r, function( key, value ) {
                    html += "<tr id='"+value.id+"'>";
                    html += "<td>" + (key+1) + "</td>";
                    html += "<td>" + value.rif + "</td>";
                    html += "<td>" + value.razon + "</td>";
                    html += "<td>" + value.dir + "</td>";
                    html += "<td>" + value.tlf + "</td>";
                    html += "<td><a class='edit icon'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span></a><a class='delete'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></a></td>";
                    html += "</tr>";
                    
                  });
                $('#companies tbody').html(html);
            }
        }
    }).fail(function(r){
        console.log(r);
            $('#msg .modal-body').html('Error consultando la(s) compañia(s)');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });
}



function putCompany() {
    
    
    var company = new Object();
    company.razon = $('#razon-txt').val();
    company.dir = $('#dir-txt').val();
    company.tlf = $('#tlf-txt').val();
    company.rif = $('#rif-txt').val();
    company.logoType = logoType;
    company.logoExt = logoExt;
    
    //company = JSON.stringify(company);
    
    
    
    $.ajax({
        url: 'controller/companies.php',
        data: {'parameters' : {'company':company}, 'method': 'putCompany' },
        method: 'post',
        dataType: 'json',
        success: function(r){
            console.log(r);
            if (!('error' in r)){
              
                $('#msg .modal-body').html(r.result);
                $('#msg').addClass('has-success');
                $('#msg').modal('toggle');
                $('#form-company')[0].reset();
                getCompanies();
            } else {
              
                $('#msg .modal-body').html(r.error);
                $('#msg').addClass('has-error');
                $('#msg').modal('toggle');
                $('#form-company')[0].reset();
            }
        }
    }).fail(function(r){
        console.log(r);
        $('#msg').html('Error getting companies');
        $('#msg').addClass('has-error');
    });
}




function editCompany() {
    console.log('edit company');
        var company = new Object();
    company.razon = $('#razon-txt').val();
    company.dir = $('#dir-txt').val();
    company.tlf = $('#tlf-txt').val();
    company.rif = $('#rif-txt').val();
    company.id = $('#id-txt').val();
    company.logoType = logoType;
    company.logoExt = logoExt;
    
    //company = JSON.stringify(company);
    $.ajax({
        url: 'controller/companies.php',
        data: {'parameters' : {'company':company}, 'method': 'editCompany' },
        method: 'post',
        dataType:'json',
        success: function(r){
            
            
            if (!('error' in r)){

                
            $('#msg .modal-body').html(r.result);
                $('#msg').addClass('success');
                $('#msg').modal('toggle');
                $('.edi-company').removeClass('edi-company').addClass('cre-company');
                $('#form-company')[0].reset();
                getCompanies();
            } else {
                
                 $('#msg .modal-body').html(r.error);
                $('#msg').addClass('success');
                $('#msg').modal('toggle');
                $('.edi-company').removeClass('edi-company').addClass('cre-company');
                $('#form-company')[0].reset();
                
            }
        }
    }).fail(function(r){
        console.log(r);
        $('#msg .modal-body').html('Error editing Company.');
                $('#msg').addClass('error');
                $('#msg').modal('toggle');
    });
}

/**
 * Comment
 */
function loadDataEditCompany(id) {
   
    var datas = $('#'+id+' td');
    
    $('#id-txt').val(id);
    $('#razon-txt').val(datas.eq(2).text());
    $('#dir-txt').val(datas.eq(3).text());
    $('#tlf-txt').val(datas.eq(4).text());
    $('#rif-txt').val(datas.eq(1).text());
   $('.cre-company').removeClass('cre-company').addClass('edi-company');
    
}


function deleteCompany(id) {
    console.log('getCompanies');
    $.ajax({
        url: 'controller/companies.php',
        data: {'parameters' : {'id':id}, 'method': 'deleteCompany' },
        method: 'post',
        dataType: 'json',
        success: function(r){
            
       
              if (!('error' in r)){

                
                $('#msg .modal-body').html(r.result);
                $('#msg').addClass('success');
                $('#msg').modal('toggle');
            
                getCompanies();
            } else {
                $('#msg .modal-body').html(r.error);
                $('#msg').addClass('success');
                $('#msg').modal('toggle');
                
            }
        }
    }).fail(function(r){
            console.log(r);
            $('#msg .modal-body').html('Error eliminando la(s) compañia(s)');
            $('#msg').addClass('success');
            $('#msg').modal('toggle');
    });
}


function uploadLogo(event) {

    var files = event.target.files;
    console.log(files);
    
    logoExt = files[0].name;
    logoType = files[0].type;
    console.log(logoExt + " : " + logoType);
    
    // Create a formdata object and add the files
    var data = new FormData();
    $.each(files, function (key, value)
    {
        
        data.append(key, value);
    });

    console.log(data);


    $.ajax({
        url: 'uploadFile.php?lg',
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
                $('#file-logo').filestyle('clear');
                errorLogo = false;
                $('#file-logo').parent().removeClass('has-error');
                //  refreshProducts();
            }
            else
            {
                // Handle errors here
                console.log('ERRORS: ' + data.error);
                $('#msg .modal-body').html(data.error);
                $('#msg').addClass('error');
                $('#msg').modal('toggle');
                $('#file-logo').focus();
                logoType = null;
                logoExt = null;
                errorLogo = true;
            }
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            // Handle errors here
            console.log('ERRORS: ' + textStatus);
            errorLogo = true;
            // STOP LOADING SPINNER
        }
    });

}



$(document).ready(function(){

    $('body').on('click', '.edit', function(){
        loadDataEditCompany($(this).parent().parent().attr('id'));
    });
    
     $('body').on('click', '.delete', function(){
        deleteCompany($(this).parent().parent().attr('id'));
    });
    
     $('body').on('click', '.find-company', function(){
         getCompanies($('#emp-find').val());
    });
    
    
    $('body').on('click', '.cre-company', function(){
        //putCompany();
    });
    
      $('body').on('click', '.edi-company', function(){
       // editCompany();
    });
    
    $('#form-company').submit(function(){
        console.log('submit');
        if (errorLogo == false){
                 
            if (!$('#btn-submit').hasClass('cre-company')){
                editCompany();
            } else {
            putCompany();
            }
        } else {
             $('#file-logo').focus();
             $('#file-logo').parent().addClass('has-error');
        }
        return false;
        
    });
    
    
    $('#file-logo').change(uploadLogo);
    getCompanies();
           
});