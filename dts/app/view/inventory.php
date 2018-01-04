<?php

    session_start();
    //var_dump($_SESSION);
    if (!$_SESSION ['keywords']) {
        echo "<script languaje='javascript'>
          alert('Disculpe, Debe ingresar su usuario y clave');
          location.href='login.php';
        </script>";
        }else if(!in_array('secur', $_SESSION ['keywords'])) {
        echo "<script languaje='javascript'>
          alert('No tiene acceso');
          location.href='index.php';
        </script>";

    }

    include '../../common/general.php';
    $obj_common = new common();
    $obj_function = new coFunction();
    $obj_bdmysql = new coBdmysql();
    $controller = "ctsecurity.php";
    $mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
    $cod_usuario = $_SESSION["cod_usuario"];


?>
<!DOCTYPE html>
<html lang="en">
  <?php $obj_common->head();?>
    <!-- bootstrap-daterangepicker -->
    <!-- Datatables -->
    <link href="../../vendors/bootstrap-fileinput-master/css/fileinput.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../../assets/dataTables/media/css/jquery.dataTables.css">
    <!-- Switchery -->

  <body class="nav-md">
    <div id="preloader" style="display:none;width:100%; height:100%; position:fixed; top:0; left:0; right:0; bottom:0; margin:auto; background: rgba(255,255,255,0.9); z-index:10000; text-align:center;">
          <div style="position:absolute; top:50%; left:50%; margin:-50px 0 0 -50px;font-size:38px;color:#00AEFF;font-style:italic;">loading...</div>
          <!--<div id="loader" style="width:128px; height:128px; position:absolute; top:50%; left:50%; margin:-50px 0 0 -50px;background:url(../../assets/img/loader.gif) center no-repeat;">&nbsp;</div>-->
      </div>
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <?php $obj_common->left_col();?>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <?php $obj_common->top_nav();?>
        </div>
        <!-- /top navigation -->

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">
            <div class="page-title">
              <div class="title_left">
                <h3>Form Open Matter Student</h3>
              </div>
            </div>
            <div class="clearfix"></div>

            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-windows fa-fw"></i> Open Matter Student
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <form>

                                    <div class="row">
                                            <div style="height: 400px" class="col-xs-6">
                                                
                                            <table id="employee_grid" class="table table-striped table-bordered"  width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th ></th>
                                                    <th>partnumber</th>
                                                    <th>binloc</th>
                                                    <th>stock</th>
                                                </tr>
                                            </thead>
                                        </table>
                                            </div>
                                            <div class="col-xs-3">
                                                
                                                      <div class="form-group">
                                                          <label>PartNo</label>
                                                          <input id="idG_invs" disabled=""  class="form-control" >
                                                      </div>
                                                      <div class="form-group">
                                                          <label>Descri </label>
                                                          <input id="SkuNos" disabled="" class="form-control"  >
                                                      </div>
                                                      <div class="form-group">
                                                          <label>Modelo</label>
                                                          <input id="MfgCodes" disabled="" class="form-control"  >
                                                      </div>
                                                      <div class="form-group">
                                                        <label>Ref</label>
                                                        <input id="PartNos" disabled="" class="form-control"  >
                                                      </div><!-- /.form-group -->


                                            </div>
                                            <div class="col-xs-3">
                                                
                                                          <label>ProImg</label>
                                                      <div class="form-group">
                                                        <img  height='170' width='180' src='../../assets/images/media.jpg' />
                                                      </div><!-- /.form-group -->
                                                      <div class="form-group">
                                                        <label>Origen</label>
                                                        <input id="PartNos" disabled="" class="form-control"  >
                                                      </div><!-- /.form-group -->

                                            </div>
                                            <div  class="col-xs-6">
                                                
                                                        <label>Binloc</label>
                                            <table id="detalle" class="table table-striped table-bordered"   width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Name Binloc &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button id="btcerrar2" type="button" class="btn btn-default" data-dismiss="modal">Add Binloc</button>
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>

                                            </div>
                                    </div>
<table id="campaign_table_list" class="uk-table uk-table-nowrap table_check" width="100%">
                            <thead>
                            <tr>
                                <th class="uk-width-1-10 uk-text-center small_col"><input type="checkbox" data-md-icheck class="check_all"></th>
                                <th class="uk-width-4-10">Name</th>
                                <th class="uk-width-4-10">Triggers</th>
                                <th class="uk-width-1-10 uk-text-center">Actions</th>
                            </tr>
                            </thead>
                        </table>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <button id="user_button_save" class="btn btn-primary" type="button"><i class="fa fa-save fa-fw"></i> Save</button>
                                            <button id="btnLimpiar" class="btn btn-primary" type="button"><i class="fa fa-eraser fa-fw"></i> Clear</button>
                                        </div>
                                    </div>                                  
                                    <!-- Modal -->
                                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="panel" style="margin-bottom: 0px;">
                                                <div class="panel-heading"> Process info </div>
                                                <div class="panel-body">
                                                <p></p>
                                                </div>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal -->
                                    
                                    <!-- Modal confirmacion -->
                                    <div class="modal fade" id="ModalConfirmacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Confirmation</h4>
                                                </div>
                                                <div class="modal-body">
                                                    You want to delete this record.
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Not</button>
                                                    <button id="btnConfirmar" type="button" class="btn btn-primary">Yes</button>
                                                </div>
                                            </div>
                                            <!-- /.modal-content -->
                                        </div>
                                        <!-- /.modal-dialog -->
                                    </div>
                                    <!-- /.modal confirmacion -->
                                </form>                                
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>


        <!-- /page content -->

        <!-- footer content -->
          <?php $obj_common->footer();?>
        <!-- /footer content -->
      </div>
    </div>

    <!-- jQuery -->
    <script src="../../vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="../../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <!-- jquery.inputmask -->
    <script src="../../vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    <script src="../../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../../vendors/nprogress/nprogress.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../../vendors/moment/min/moment.min.js"></script>
    <!-- Datatables -->
        <script type="text/javascript" language="javascript" src="../../assets/dataTables/media/js/jquery.dataTables.js"></script>
    <script src="../../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../../vendors/pdfmake/build/vfs_fonts.js"></script>
    <!--PRELOADER-->
    <script src="../js/ctDtsInventory.js" type="text/javascript"></script>
    <script src="../../assets/js/preloader.js" type="text/javascript"></script>
    <!-- Switchery -->
    <!-- jQuery autocomplete -->
   
    <!-- Custom Theme Scripts -->
    <script src="../../build/js/custom.js?v=3"></script>

        <script>
            var $G_id  = '';
            var method = '';
            var $Listar, $limpiar, $mostrarMsj;
            $(document).ready(function(){
            var method = '';

            $('#sel_oms').change(function(){
            $Listar();
            });

            var tables = $('#employee_grid').DataTable({
                    "columnDefs": [
                        {
                            "targets": [ 0 ],
                            "visible": false,
                            "searchable": false
                        }
                    ],"dom": '<lf<t>ip>',
                             "bProcessing": true,
                     "serverSide": true,
                     "ajax":{
                        url :"response.php", // json datasource
                        type: "post",  // type of method  ,GET/POST/DELETE
                        error: function(){
                          $("#employee_grid_processing").css("display","none");
                        }
                      }
                    });   
           



            $('#btnLimpiar').click(function(){$limpiar();});
            $("#user_button_save").click(function() {
                if(validar()){                
                method = 'editOMS';
                $.post("../controllers/<?php echo $controller;?>",
                    {
                        "method":method,
                        "id":$G_id,
                        "openmatter_id":$('#sel_oms').val(),
                        "student_id":$('#sel_student').val()
                    },
                    function(data){
                        if(data.mss == '1'){
                    $mostrarMsj('success',data.salida);
                            $Listar();
                            $limpiar();
                        }else{
                            console.log(data.salida);
                        }
                        //desactiva_preloader();
                },"json")
                .fail(
                    function(error) {
                        console.log(error.responseJSON)
                    }
                );}
            });

            function validar(){
                //validar los datos requeridos
                if ($('#txtUserPass').val() != $('#ConfirUserPass').val()){
                    $mostrarMsj('danger','Passwords do not match.');
                    $('#txtUserPass').focus();
                    return false;
                }
                if ($('#txtname').val() == ''){
                    $mostrarMsj('danger','Please enter the name');
                    $('#txtname').focus();
                    return false;
                }
                if ($('#txtuser_name').val() == ''){
                    $mostrarMsj('danger','Please enter the user name');
                    $('#txtuser_name').focus();
                    return false;
                }
                if ($('#sel_Role').val() == ''){
                    $mostrarMsj('danger','Please enter the user name');
                    $('#sel_Roles').focus();
                    return false;
                }
                return true;
            }
 
            $('#employee_grid').on( 'click', 'tr', function () {
                                var data=$(this).closest("tr");
                                var idG_inv = tables.row(this).data()[0];
                                var SkuNo = data.find("td:eq(0)").text();
                                var MfgCode =data.find("td:eq(1)").text();
                                var PartNo =data.find("td:eq(2)").text();
                                //alert( 'The cell clicked on had the value of '+idG_inv );
                                $('#idG_invs').val(idG_inv);
                                $('#SkuNos').val(SkuNo);
                                $('#MfgCodes').val(MfgCode);
                                $('#PartNos').val(PartNo);

                    $Listar();
            } );



            $Listar = function(){
                alert("paso");
                    $.post("../controllers/<?php echo $controller;?>",
                        {method:"ListB",id_co_art:$('#idG_invs').val()}
                        ,function(data){
                            var html = '';
                            $('#detalle').dataTable().fnDestroy();
                            $('.fila').remove('');
                            $('.odd').remove('');
                            $.each(data.salida, function(cant_reg,detalle){//se recorre el json.
                                html += '<tr class="fila">'
                                     +       '<td>'+detalle.id+'</td>'
                                     +       '<td>'+detalle.name+'</td>'
                                     +       '<td></td>'
                                     +       '<td class="tooltip-demo"><button class="btn btn-default btnEditar" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Editar"><i class="fa fa-edit fa-fw"></i></button>';
                                if (detalle.cerrado == 'f' || detalle.re_abierto == 't'){
                                    html += ' <button class="btn btn-defau  lt btnCerrar" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Cerrar"><i class="fa fa-lock fa-fw"></i></button>';
                                }else{
                                    html += ' <button class="btn btn-default btnEliminar" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Eliminar"><i class="fa fa-eraser fa-fw"></i></button>'
                                }
                                html += '</td></tr>';
                                        });
                                $('#detalle tbody').append(html);
                                
                                table = $('#detalle').DataTable( {
                                    retrieve: true,
                                    responsive: true,
                                    paging: false,
                                    columnDefs: [{
                                            "visible" : false, 
                                            "targets": -2
                                        },
                                        {
                                            "visible" : false, 
                                            "targets": [0,3]
                                        }]
                                });            
                                // tooltip demo
                                $('.tooltip-demo').tooltip({
                                    selector: "[data-toggle=tooltip]",
                                    container: "body"
                                })

                            },"json");

            }


            $limpiar = function(){
                $G_id = '';
            $("#sel_oms").select2('val', 'All');
            $("#sel_student").select2('val', 'All');
            }

            $('#detalle tbody').on( 'click', '.btnEditar', function () {
                var data = table.row( $(this).parents('tr') ).data();
                $limpiar();
                $G_id = data[0]; // Find the text
                var datos = data[3].split(',');
                $('#sel_oms').select2('val', datos[0]);
                $('#sel_student').select2('val', datos[1]);
            });

            $('#detalle tbody').on( 'click', '.btnEliminar', function () {
                var data = table.row( $(this).parents('tr') ).data();
                $G_id = data[0]; // Find the text
                $('#ModalConfirmacion').modal('show');
            });

            $("#btnConfirmar").click(function() {
                $('#ModalConfirmacion').modal('hide');
                    $.post("../controllers/<?php echo $controller;?>",
                        {method:"deleteOMS",
                        "id":$G_id
                    },function(data){                 
                    $mostrarMsj('success',data.salida);
                    $Listar();
                },"json");
            });

            $mostrarMsj = function(tipo,msn){
                switch (tipo){
                    case 'success':
                        classes = 'panel-success';
                        break;
                    case 'info':
                        classes = 'panel-info';
                        break;
                    case 'warning':
                        classes = 'panel-warning';
                        break;
                    case 'danger':
                        classes = 'panel-danger';
                        break;
                }
                $('.modal-dialog p').html(msn);
                $('.modal-dialog .panel').addClass(classes);
                $('#myModal').modal('show');
            }
        });
        </script>
  <!--END SCRIPT-->

  </body>
</html>