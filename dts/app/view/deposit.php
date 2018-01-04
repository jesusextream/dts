<?php
    include '../../common/general.php';
    $obj_common = new common();
    $obj_function = new coFunction();
    $obj_bdmysql = new coBdmysql();
    $controller = 'ctInventory.php';

    //var_dump($_GET);//die();
    if (isset($_GET['selBD'])){
      $BD = $_GET['selBD'];
    }else{
      $BD = "ISKRO_A";
    }

    $serverName = "iskro.no-ip.org"; //serverName\instanceName
    $connectionInfo = array( "Database"=>$BD, "UID"=>"roy", "PWD"=>"ry2016");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);

    if (!$conn) {
      var_dump( print_r( sqlsrv_errors(), true));
    }

    //init CategoryList
    $qwery = "SELECT co_lin AS id, lin_des AS name FROM lin_art";
    $resultCategoryList = sqlsrv_query($conn, $qwery);
    if(!$resultCategoryList) {
      var_dump( print_r( sqlsrv_errors(), true) );
    }
    $j = 0;
    while( $row = sqlsrv_fetch_array($resultCategoryList) ) {
      $CategoryList[$j] = $row;
      $j++;
    }
    $CountCategory = $j;
    sqlsrv_free_stmt($resultCategoryList);
    //fin CategoryList

    //SUB-CATAGORIAS
    $resultSubCategoryList = '<option value="">Seleccione Categoria...</option>';
    
    
    $out[0] = null;

    if (isset($_GET['inputFrom']) && isset($_GET['inputTo'])){
      $dateFrom = date("Y-m-d", strtotime($_GET['inputFrom']) );
      $dateTo = date("Y-m-d", strtotime($_GET['inputTo']) );
      $co_lin = trim($_GET['selCategory']);
      $co_subl = trim($_GET['selSubcategory']);

      if($co_subl == '' && $co_lin == ''){
        $criterio = "";
      }else if($co_subl == '' && $co_lin != ''){
        $criterio = " AND co_lin = '".$co_lin."'";
      }else if($co_subl != '' && $co_lin != ''){
        $criterio = " AND co_lin = '".$co_lin."' AND co_subl = '".$co_subl."'";
      }
      //$criterio = "";
      
      $qwery = "SELECT count(*) AS QtySales, COUNT(DISTINCT fa.co_cli) AS QtyCustumers, 
        COUNT(DISTINCT rf.fact_num) AS QtyInvoice, rf.co_art, AVG(rf.prec_vta2) AS prec_vta2, art.art_des, art.stock_act 
        FROM [factura] fa 
        JOIN reng_fac rf ON fa.fact_num = rf.fact_num 
        JOIN art ON rf.co_art = art.co_art 
        WHERE (fa.fec_emis BETWEEN  convert(datetime, '".$dateFrom." 00:00:00', 120)
        AND  convert(datetime, '".$dateTo." 23:00:00', 120)) AND anulada = 0 ".$criterio
        ." GROUP BY rf.co_art, art.art_des, art.stock_act";
      //echo ($qwery);
      $result = sqlsrv_query( $conn, $qwery, array(), array( "Scrollable" => 'static' ));
      if(!$result) {
        die( print_r( sqlsrv_errors(), true) );
      }

      // Get result count:
      $Count = sqlsrv_num_rows ($result);
      //var_dump($Count);
      $j = 0;
      while ($row = sqlsrv_fetch_array($result)) {
        $mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
        if(!$mysqli->connect_error){
          //inventory dts skuNo Qty
          $ordersDetail = $obj_bdmysql->select(
              "`inventory dts` id 
              JOIN Inventory i ON id.SkuNo = i.SkuNo", 
              "id.SkuNo, MAX(Qty) AS QtyAviDTS, (`OnHand` - `InPick`) AS QtyAviTex", 
              "id.PartNo_DTS = '".$row['co_art']."'", 
              "",
              "",
              $mysqli);

          if (is_array($ordersDetail)){
            if (is_null($ordersDetail[0]['SkuNo'])){
              $row['QtyAviDTS'] = 0;
              $row['QtyAviTex'] = 0;
            }else{
              $row['QtyAviDTS'] = intval($ordersDetail[0]['QtyAviDTS']);
              $row['QtyAviTex'] = intval($ordersDetail[0]['QtyAviTex']);
            }
          }

        }

        $out[$j] = $row;

        $j++;

      }
      // Limpieza
      sqlsrv_free_stmt($result);

    }
    


?>
<!DOCTYPE html>
<html lang="en">
  <?php $obj_common->head();?>
    <!-- bootstrap-daterangepicker -->
    <link href="../../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <!-- Datatables -->
    <link href="../../vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../../vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../../vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../../vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../../vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

  <body class="nav-md">
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
                <h3>Form Validation</h3>
              </div>

              <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">Go!</button>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">

                <!-- form date pickers -->
                <div class="x_panel" style="">
                  <div class="x_title">
                    <h2>Inventoy <small> Select options</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>

                  <div class="x_content">
                    <div class="well row calendar-exibit" style="overflow: auto">

                      <!---<form class="form-horizontal form-label-left" novalidate>-->

                        <div class="col-md-6 control-group">
                          <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name">From <span class="required">*</span></label>
                          <div class="col-md-6 xdisplay_inputx form-group has-feedback">
                            <input id="inputFrom" name="inputFrom" type="text" class="form-control has-feedback-left" aria-describedby="inputSuccess2Status4">
                            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                            <span id="inputSuccess2Status4" class="sr-only">(success)</span>
                          </div>
                        </div>

                        <div class="col-md-6 control-group">
                          <label class="control-label col-md-4 col-sm-4 col-xs-12" for="name">To <span class="required">*</span></label>
                          <div class="col-md-6 xdisplay_inputx form-group has-feedback">
                            <input id="inputTo" name="inputTo" type="text" class="form-control has-feedback-left" aria-describedby="inputSuccess2Status4">
                            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                            <span id="inputSuccess2Status4" class="sr-only">(success)</span>
                          </div>
                        </div>

                        <div class="col-md-6 control-group">
                          <label class="control-label col-md-4 col-sm-4 col-xs-12">Category</label>
                          <div class="col-md-8 col-xs-12">
                            <input id="inputCategory" name="inputCategory" type="text" class="form-control">
                            <input id="inputHddCategory" name="inputHddCategory" type="hidden" value="">
                            <!--<select id="selCategory" name="selCategory" class="form-control">
                              ?php
                                $html = '<option value="" selected>Select All</option>';
                                foreach ($CategoryList as $key => $value) {
                                  $id = trim($value['id']);
                                  $name = trim($value['name']);
                                  $html .= "<option value='".$id."' selected>".$name."</option>";
                                }
                                echo $html;
                              ?>
                            </select>-->
                          </div>
                        </div>

                        <div class="col-md-6 control-group">
                          <label class="control-label col-md-4 col-sm-4 col-xs-12">Subcategory</label>
                          <div class="col-md-8 col-xs-12">
                            <div id="divSubCategory" class="form-control" style="overflow:auto;height:150px;"><?php echo $resultSubCategoryList; ?></div>
                          </div>
                        </div>

                        <div class="col-md-6 control-group">
                          <label class="control-label col-md-4 col-sm-4 col-xs-12">Select BD</label>
                          <div class="col-md-8 col-xs-12">
                            <select id="selBD" name="selBD" class="form-control">
                              <option value="ISKRO_A" selected>ISKRO_A</option>
                              <!--<option value="SAISKCA">SAISKCA</option>-->
                            </select>
                          </div>
                        </div>

                        <div class="col-md-12 control-group">
                        <div class="ln_solid"></div>
                        </div>


                        <div class="control-group">
                          <div class="col-md-11 col-md-offset-1">
                            <button type="submit" class="btn btn-primary">Cancel</button>
                            <button id="btnSend" type="submit" class="btn btn-success">Submit</button>
                          </div>
                        </div>

                      <!--</form>-->

                    </div>

                  </div>
                </div>
                <!-- /form datepicker -->


              </div>



              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Detail <small>invoice</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatableDetails" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>PartNo</th>
                          <th>Description</th>
                          <th>QtyAvi</th>
                          <th>SalesPrice</th>
                          <th>QtyOrders</th>
                          <th>QtySales</th>
                          <th>QtyCustumers</th>
                          <th>QtyInvoice</th>
                          <th>QtyAvi-DTS</th>
                          <th>QtyAvi-Tex</th>
                        </tr>
                      </thead>


                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
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
    <script src="../../vendors/fastclick/lib/fastclick.js"></script>
    <!-- NProgress -->
    <script src="../../vendors/nprogress/nprogress.js"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="../../vendors/moment/min/moment.min.js"></script>
    <script src="../../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

    <!-- Datatables -->
    <script src="../../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../../vendors/pdfmake/build/vfs_fonts.js"></script>
    <!-- jQuery autocomplete -->
    <script src="../../vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
    
    <!-- Custom Theme Scripts -->
    <script src="../../build/js/custom.js?v=3"></script>

    <script>
      $(document).ready(function(){

        var handleDataTableButtons = function() {
          if ($("#datatableDetails").length) {
          var table = $("#datatableDetails").DataTable({
            dom: "Bfrtip",
            buttons: [
            {
              extend: "copy",
              className: "btn-sm"
            },
            {
              extend: "csv",
              className: "btn-sm"
            },
            {
              extend: "excel",
              className: "btn-sm"
            },
            {
              extend: "pdfHtml5",
              className: "btn-sm"
            },
            {
              extend: "print",
              className: "btn-sm"
            },
            ],
            responsive: true
          });
          }
        };

        TableManageDetails = function() {
          "use strict";
          return {
          init: function() {
            handleDataTableButtons();
          }
          };
        }();

        TableManageDetails.init();

        var toMmDdYy = function(input) {
            var ptrn = /(\d{4})\-(\d{2})\-(\d{2})/;
            if(!input || !input.match(ptrn)) {
                return null;
            }
            return input.replace(ptrn, '$2/$3/$1');
        };

        $('#datatableDetails tr > *:nth-child(5)').toggle();
        

     
        /* AUTOCOMPLETE */
        function init_autocomplete() {
      
          if( typeof ($.fn.autocomplete) === 'undefined'){ return; }
          console.log('init_autocomplete');          
          var Category = {
            <?php
              
              foreach ($CategoryList as $key => $value) {
                echo "'".trim($value['id'])."':'".trim($value['name'])."'";
                if($con < $CountCategory){
                  echo ", ";
                }
                $con++;
              }

            ?>
          };

          var CategoryArray = $.map(Category, function(value, data) {
            return {
            value: value,
            data: data
            };
          });

          // initialize autocomplete with custom appendTo
          $('#inputCategory').autocomplete({
            lookup: CategoryArray,
            onSelect: function (suggestion) {
              AutoCompleteSelectHandler(suggestion.data, suggestion.value);
            }
          });
          
        };

        init_autocomplete();

        //CARGA SELECT SUBCATEGORIA DE A PARTIR DE LA CATEGORIA
        function AutoCompleteSelectHandler(co_lin,lin_des) {
            $('inputHddCategory').val(co_lin);
            $("#divSubCategory").html('<input type="checkbox" id="catalogo_ch_'+co_lin+'" value="'+co_lin+'"> <label for="catalogo_ch_'+co_lin+'">'+lin_des+.'</label> <br>');            
        }

        //BUSCA ARTICULO
        $( "#btnSend" ).click(function() {
          var catalogo_categoria = '';
          var catalogo_subcategoria = '';
          var catalogo_ventaFrom = '';
          var catalogo_ventaTo = '';

          $('#divSubCategory').children('input').each(function(){
            if(this.checked == true){
              if(catalogo_subcategoria != ''){
                catalogo_subcategoria += ",";
                catalogo_subcategoria += '\''+this.value+'\'';
              }else{
                catalogo_subcategoria += '\''+this.value+'\'';
              }
            }         
          });

          opc = "catalogoArtBusca";
          catalogo_categoria = $('#inputHddCategory').val();
          catalogo_ventaFrom = $('#inputFrom').val();
          catalogo_ventaTo = $('#inputTo').val();

          $.post("../controllers/<?php echo $controller;?>",{
            "opc":opc
            ,"catalogo_categoria":catalogo_categoria
            ,"catalogo_subcategoria":catalogo_subcategoria
            ,"catalogo_ventaFrom" : catalogo_ventaFrom
            ,"catalogo_ventaTo" : catalogo_ventaTo
          },function(data){
              if(data.mss === '1'){
                  //oTable.destroy();
                  $('#datatableDetails tbody').html('');
                  //$('#filadetails').remove();$('.odd').remove();
                  $('#datatableDetails').dataTable().fnClearTable();
                  $('#datatableDetails').dataTable().fnDestroy();
                  var html = '';
                  $.each( data.salida, function(i, row) {
                      html += "<tr id='filadetails'>"
                        + "<th>"+row['co_art']+"</th>"
                        + "<th>"+row['art_des']+"</th>"
                        + "<th>"+row['stock_act']+"</th>"
                        + "<th>"+row['prec_vta2']+"</th>"
                        + "<th>0</th>"
                        + "<th>"+row['QtySales']+"</th>"
                        + "<th>"+row['QtyCustumers']+"</th>"
                        + "<th>"+row['QtyInvoice']+"</th>"
                        + "<th>"+row['QtyAviDTS']+"</th>"
                        + "<th>"+row['QtyAviTex']+"</+ " 
                        + "</tr>";
                  });
                  $('#datatableDetails tbody').append(html);
                  TableManageDetails.init();
              }else{ 
                  console.log(data);
                  $('#datatableDetails tbody').html('');
              }
          },"json");
                
        });

    });
  </script>

  <!--END SCRIPT-->

  </body>
</html>