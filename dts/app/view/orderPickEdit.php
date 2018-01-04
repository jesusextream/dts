<?php
    include '../../common/general.php';
    $obj_common = new common();
    $obj_function = new coFunction();
    $obj_bdmysql = new coBdmysql();
    $controller = 'ctOrder.php';
    $mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
    if (isset($_GET['r'])){
        if(!$mysqli->connect_error){
            $OrdID = $_GET['r'];
            //inventory dts skuNo Qty
            $ordersDetail = $obj_bdmysql->select(
                "`orders detail` od 
                JOIN Inventory i ON od.SkuNo = i.SkuNo
                LEFT JOIN `codes catsub` ccs ON od.PrdCode = ccs.PrdCode", 
                "od.LineID, od.Ord, od.MfgCode, od.PartNo, od.Shp, od.QtyReserve, od.QtyDts, i.BinLoc, od.SkuNo, 
                (SELECT MAX(Qty) FROM `inventory dts` WHERE SkuNo = od.SkuNo) AS availableDts", 
                "OrdID=" . $OrdID . " ORDER BY ccs.PrdDesc", 
                "",
                "",
                $mysqli);

            if (is_array($ordersDetail)){

                $BillName = $_GET['BillName'];
                $OrdNo = $_GET['OrdNo'];
                $option = $_GET['option'];

                //var_dump($ordersDetail);
                if ($ordersDetail !== ''){
                    $data = $ordersDetail;
                }

            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
    <?php 
    //include '../../common/head.php';
    $obj_common->head();?>
    <link type="text/css" rel="stylesheet" href="../../assets/bootstrap-fileinput-master/css/fileinput.css" />
    <link type="text/css" rel="stylesheet" media="all" href="../../assets/bootstrap-colorpickersliders-master/bootstrap.colorpickersliders.css">
    <link type="text/css" rel="stylesheet" media="all" href="../../assets/css/jquery-ui-1.9.2.custom.min.css">
    

    <body>
        <div id="modal" style="width:100%; height:100%; position:fixed; top:0; left:0; right:0; bottom:0; margin:auto; padding:10px;background:rgba(0,0,0,0.6); z-index:9000; text-align:center;display:none;">&nbsp;</div>
        <div id="preloader" style="display:none;width:100%; height:100%; position:fixed; top:0; left:0; right:0; bottom:0; margin:auto; background: rgba(255,255,255,0.9); z-index:10000; text-align:center;">
            <div style="position:absolute; top:50%; left:50%; margin:-50px 0 0 -50px;font-size:38px;color:#00AEFF;font-style:italic;">Cargando...</div>
            <!--<div id="loader" style="width:128px; height:128px; position:absolute; top:50%; left:50%; margin:-50px 0 0 -50px;background:url(../../assets/img/loader.gif) center no-repeat;">&nbsp;</div>-->
        </div>
        <section id="container" class="sidebar-closed">
            <!-- TOP BAR CONTENT & NOTIFICATIONS -->
            <?php $obj_common->header();?>

            <!-- MAIN SIDEBAR MENU -->
            <?php $obj_common->left_sidebar($_SERVER['PHP_SELF']);?></aside>

            <!-- MAIN CONTENT -->
            <section id="main-content">
                <section class="wrapper">
                    
                    
                    <h3><i class="fa fa-angle-right"></i> <a href="catalogoIndex.php">Order Pick</a> <i class="fa fa-angle-right"></i> Detail</h3>
                    <!-- BASIC FORM ELELEMNTS -->
                    <div class="row mt">
                        <div class="col-lg-12">
                            <div class="form-panel">
                                <form class="form-horizontal style-form" method="get" id="catalogo_form">
                                    <!--ACORDION-->
                                    <div class="panel-group" id="accordion">
                                        <!--ACORDION 1-->
                                        <div class="panel panel-default">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                                            <div class="panel-heading" style="background:#F5F5F5;">
                                              <h4 class="panel-title"><i class="fa fa-database"></i> Order #:  <?php echo $OrdNo; ?>, Custumer: <?php echo $BillName; ?></h4>
                                            </div>
                                            </a>
                                            <div id="collapse1" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    <div class="form-group" style="margin-bottom: 0px; padding-bottom: 0px;">
                                                        <div class="col-sm-6">
                                                            <div class="text-center">
                                                                <input id="inputQtyProducts" class="knob" data-width="150" data-min="0" data-displayPrevious=true data-max="100" data-step="100" value="1" data-fgColor="#61C0E6" data-weight="150"/>
                                                                <!--<input class="knob" data-width="150" data-angleOffset="90" data-linecap="round" data-fgColor="#61C0E6" value="100"/>-->
                                                            </div>
                                                            <h4 class="text-center">Qty Products (<label id="lblCount"></label>)</h4>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="text-center">
                                                                <input id="inputQtyItems" class="knob" data-width="150" data-min="0" data-displayPrevious=true data-max="100" data-step="100" value="1" data-fgColor="#5cb85c" data-weight="150"/>
                                                                <!--<input  id="inputQtyItems"class="knob" data-width="150" data-angleOffset="90" data-linecap="round" data-fgColor="#5cb85c" value="35"/>-->
                                                            </div>
                                                            <h4 class="text-center">Qty Items (<label id="lblCountItems"></label>)</h4>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                            </div>
                                        </div>
                                        <!--FIN ACORDION 1-->

  
                                    </div>
                                    <!--FIN ACORDION-->
                                    <!--TABLA DE ARTICULOS CARGADOS-->
                                    <div class="form-group">
                                        <div class="col-lg-12">                                            
                                            <div class="form-control" style="height: 600px;overflow:auto;position:relative;">
                                                <section id="no-more-tables">                                                   
                                                    <table id="tableOrderDetail" class="table table-advance table-hover" border="0">
                                                        <h4><i class="fa fa-angle-right"></i> DETAILS</h4>
                                                        <hr>
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center"> LineID</th>
                                                                <th class="text-center"> Qty</th>
                                                                <th class="text-center"> Mfg</th>
                                                                <th class="text-center"> PartNro</th>
                                                                <th class="text-center"> BinLoc</th>
                                                                <th class="text-center"> Ship</th>
                                                                <th class="text-center"> Total</th>
                                                                <th class="text-center"> Dts</th>
                                                                <th class="text-center"> availableDts</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 

                                                                if (isset($ordersDetail) && is_array($ordersDetail)){
                                                                    $html = "";
                                                                    $count=0;
                                                                    $countItems=0;
                                                                    $totalQtyProducts = 0;
                                                                    $totalQtyItems = 0;
                                                                    foreach ($ordersDetail as $key => $value) {
                                                                        //od.Ord, od.MfgCode, od.PartNo, od.Shp, od.QtyReserve, od.QtyDts, i.BinLoc
                                                                        $count=$count+1;
                                                                        $LineID = $value['LineID'];
                                                                        $Ord = $value['Ord'];
                                                                        $MfgCode = $value['MfgCode'];
                                                                        $PartNo = $value['PartNo'];
                                                                        $Shp = $value['Shp'];
                                                                        $QtyReserve = intval($value['QtyReserve']);
                                                                        $QtyDts = intval($value['QtyDts']);
                                                                        $BinLoc = $value['BinLoc'];
                                                                        $SkuNo = $value['SkuNo'];
                                                                        $availableDts = intval($value['availableDts']);

                                                                        //totales
                                                                        $countItems=$countItems+$Ord;
                                                                        if($QtyReserve > 0 || $QtyDts > 0){
                                                                            $totalQtyProducts = $totalQtyProducts+1;
                                                                            $totalQtyItems = $totalQtyItems+ $QtyReserve+$QtyDts;
                                                                        }

                                                                        $html =  $html
                                                                            ."<tr id='".$LineID."'>
                                                                                <td class='text-center'> $LineID</td>
                                                                                <td class='text-center'> $Ord</td>
                                                                                <td class='text-center'> $MfgCode</td>
                                                                                <td class='text-center'> <a 
                                                                                    data-toggle='tooltip' 
                                                                                    title=\"<img  height='180' width='180' src='http://50.196.74.121/textronic/Imagenes-Importadas-sku/".$SkuNo.".jpg' />\">$PartNo</a></td>
                                                                                <td class='text-center'> $BinLoc</td>
                                                                                <td class='text-center'> $Shp</td>
                                                                                <td class='text-center'> $QtyReserve</td>
                                                                                <td class='text-center'> $QtyDts</td>
                                                                                <td class='text-center'> $availableDts</td>
                                                                            </tr>";
                                                                    }
                                                                    echo $html;
                                                                } ?>
                                                        </tbody>
                                                    </table>
                                                </section>
                                            </div><!-- /content-panel -->
                                        </div><!-- /col-lg-12 -->
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <div class="col-sm-12" align="right">
                                            <button type="button" class="btn btn-default" onclick="ir_a('orderPick.php','')">Cancel</button>
                                            <button type="button" class="btn btn-success" onclick="">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div><!-- col-lg-12-->      	
                    </div><!-- /row -->
                    
                    
                </section>
                </section>
            </section>

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Process order detail</h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-3">
                                <input id="inputOrd" type="hidden">
                                <input id="inputOrdID" type="hidden">
                                <input id="inputLineID" type="hidden">
                                <input id="inputAvailableDts" type="hidden">
                                <label class="control-label"><strong>Qty</strong></label>
                                <p id="lblOrd" class="control-label"></p>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label"><strong>Mfg</strong></label>
                                <p id="lblMfgCode" class="control-label"></p>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label"><strong>PartNro</strong></label>
                                <p id="lblPartNo" class="control-label"></p>
                            </div>
                            <div class="col-sm-3">
                                <label class="control-label"><strong>BinLoc</strong></label>
                                <p id="lblBinLoc" class="control-label"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label"><strong>QtyReserve</strong></label>
                                <input type="text" id="inputQtyReserve" class="keyboard keyboard-numpad form-control">
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label"><strong>QtyDts</strong></label>
                                <input type="text" id="inputQtyDts" class="form-control">
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="btnSave" type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalSkuNo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <img id="imgSkuNo" src="">
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

            <!--FOOTER-->
            <?php $obj_common->footer();?>
        
        </section>
        <!--JAVASCRIPT GENERAL-->
        <?php $obj_common->script();?>
        <!--JAVACRIPT LOCAL-->
        <!--ESTILO DE INPUT FILE-->
        <script src="../../assets/bootstrap-fileinput-master/js/fileinput.js" type="text/javascript"></script>
        <script src="../../assets/js/bootstrap-switch.js"></script>
        <script src="../../assets/js/sortTable.js"></script>
        <script src="../../assets/js/funcionesCat.js" type="text/javascript"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/tinycolor/0.11.1/tinycolor.min.js"></script>
        <script src="../../assets/bootstrap-colorpickersliders-master/bootstrap.colorpickersliders.js" type="text/javascript"></script>
        <script src="../../assets/js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../assets/js/knob/jquery.knob.min.js"></script>
        
        <!--INICIALIZACION
        <script src="../js/flyer.js"></script>-->

        <script>
            $(document).ready(function(){

                $('#inputQtyReserve').keyboard({type:'numpad'});
                $('#tableOrderDetail tr > *:nth-child(1)').toggle();
                $('#tableOrderDetail tr > *:nth-child(9)').toggle();                
                $('#tableOrderDetail td').on('click', function() {
                    if ($(this).index() == 3) {
                        return false; // disable 3rd column
                    }
                });
                $('#inputQtyDts').prop('disabled', true);

                $('#tableOrderDetail').find('tr').click( function(){
                    var data=$(this).closest("tr"); 
                    var LineID = data.find("td:eq(0)").text();
                    var Ord = data.find("td:eq(1)").text();
                    var MfgCode =data.find("td:eq(2)").text();
                    var PartNo = data.find("td:eq(3)").text();
                    var BinLoc = data.find("td:eq(4)").text();
                    var QtyReserve = data.find("td:eq(6)").text();
                    var QtyDts = data.find("td:eq(7)").text();
                    var availableDts = data.find("td:eq(8)").text();

                    $('#inputLineID').val(LineID);
                    $('#inputOrd').val(Ord);
                    $('#lblOrd').html(Ord);
                    $('#lblMfgCode').html(MfgCode);
                    $('#lblPartNo').html(PartNo);
                    $('#lblBinLoc').html(BinLoc);
                    $('#inputQtyReserve').val(QtyReserve);
                    $('#inputQtyDts').val(QtyDts);
                    $('#inputAvailableDts').val(availableDts);

                    $("#myModal").modal('show');
                });

                $( "#btnSave" ).click(function() {

                    opc = 'saveOrderDetail';
                    activa_preloader();

                    $.post("../controllers/<?php echo $controller;?>",
                        {
                            "opc":opc,
                            "LineID":$('#inputLineID').val(),
                            "QtyReserve":$('#inputQtyReserve').val(),
                            "QtyDts":$('#inputQtyDts').val(),
                            "OrdID":$('#inputOrdID').val()
                        },
                        function(data){
                            if(data.mss == '1'){
                                $("#"+data.LineID+" td").eq(6).html(data.QtyReserve);
                                $("#"+data.LineID+" td").eq(7).html(data.QtyDts);
                                var Ord = parseInt($("#"+data.LineID+" td").eq(1).text());
                                var availableDts = parseInt ($("#"+data.LineID+" td").eq(8).text());

                                $("#"+data.LineID).removeClass();
                                if ((parseInt(data.QtyReserve) + parseInt(data.QtyDts)) != 0){
                                    if (Ord == parseInt(data.QtyReserve)){
                                        $("#"+data.LineID).addClass("table-tr-green");
                                    }else{
                                        $("#"+data.LineID).addClass("table-tr-yellow");
                                    }
                                }else if (availableDts > 0){
                                    $("#"+data.LineID).addClass("table-tr-orange");
                                }

                                //totales
                                $('#inputQtyProducts').val(data.totalQtyProducts);
                                $('#inputQtyProducts').attr('data-max',data.count);
                                $('#lblCount').html('<strong>'+data.count+'</strong>');

                                $('#inputQtyItems').val(parseInt(data.totalQtyReserve) + parseInt(data.totalQtyDts));
                                $('#inputQtyItems').attr('data-max',data.countItems);
                                $('#lblCountItems').html('<strong>'+data.countItems+'</strong>');

                                $("#myModal").modal('hide');
                            }else{
                                console.log(data.mss);
                            }
                            desactiva_preloader();
                    },"json")
                    .fail(
                        function(error) {
                            console.log(error.responseJSON)
                        }
                    );
                });

            });

            $('#inputQtyProducts').val(<?php echo $totalQtyProducts;?>);
            $('#inputQtyProducts').attr('data-max',<?php echo $count;?>);
            $('#lblCount').html('<strong><?php echo $count;?></strong>');

            $('#inputQtyItems').val(<?php echo $totalQtyItems;?>);
            $('#inputQtyItems').attr('data-max',<?php echo $countItems;?>);
            $('#lblCountItems').html('<strong><?php echo $countItems;?></strong>');

            $('#inputOrdID').val(<?php echo $OrdID; ?>);
            
            //Start Knob Plugin
            var uiKnob = function(){
                if($(".knob").length > 0){
                    $(".knob").knob();
                }
                
            }//End Knob

            var formElements = function(){
                return {
                    init: function(){
                        uiKnob();
                    }
                }
            }();

            formElements.init();

            var tr_hover = function(){
                //$(this).addClass("tr_hover");
                $("#tableOrderDetail tbody tr").each(function (index) 
                {
                    var QtyReserve, QtyDts, availableDts, Ord;
                    $(this).children("td").each(function (index2) 
                    {
                        switch (index2) 
                        {
                            case 1: 
                                Ord = $(this).text();
                                break;
                            case 6: 
                                QtyReserve = $(this).text();
                                break;
                            case 7: 
                                QtyDts = $(this).text();
                                break;
                            case 8: 
                                availableDts = $(this).text();
                                break;
                        }
                    })
                    $(this).removeClass();
                    if (parseInt(QtyReserve) > 0 || parseInt(QtyDts) > 0){
                        if( (parseInt(QtyReserve)) == Ord ){
                            $(this).addClass("table-tr-green");
                        }else{
                            $(this).addClass("table-tr-yellow");
                        }
                    }else if(parseInt(availableDts) > 0){
                        $(this).addClass("table-tr-orange");
                    }
                })
            }

            tr_hover();

            $('a[data-toggle="tooltip"]').tooltip({
                animated: 'fade',
                placement: 'bottom',
                html: true
            });

            $('#inputQtyReserve').change(function(e)  {

                var AvailableDts = parseFloat($('#inputAvailableDts').val());
                var Ord = parseFloat($('#inputOrd').val());
                var QtyReserve = parseFloat($('#inputQtyReserve').val());
                var Total = 0;

                if (AvailableDts > 0){
                    if (Ord > QtyReserve){
                        Total = Ord - QtyReserve;
                        if (AvailableDts > Total){
                            $('#inputQtyDts').val(Total);
                        }else{
                            $('#inputQtyDts').val(AvailableDts);
                        }
                    }else{
                        $('#inputQtyDts').val(0);
                    }
                }
            });

        </script>
     
        <!--END SCRIPT-->
    </body>
</html>
