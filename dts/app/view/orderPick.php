<?php
    include '../../common/general.php';
    $obj_common = new common();
    $obj_function = new coFunction();
    $obj_bdmysql = new coBdmysql();
    $controller = 'ctOrder.php';
    $mysqli = new mysqli(DBHOST2, DBUSER2, DBPASS2, DBNOM2);
    /*if (!$mysqli->connect_error){
        //DATOS DEL CATALOGO
        $mss = '';
        if($obj_bdmysql->num_row("catalogo", "", $mysqli) > 0){
            $resul = $obj_bdmysql->select("catalogo", "*,DATE_FORMAT(fe_us_in,'%d/%m/%Y') as fe_us_in_dmy", "", "", "",$mysqli);
            //echo $resul;
            if(!is_array($resul)){ $mss = 'NO SE ENCONTRARON DATOS.'; }
        }else{
            $mss = "NO SE ENCONTRARON CATALOGOS REGISTRADOS.";
        }
    }*/
?>
<!DOCTYPE html>
<html lang="en">
    <?php 
    //include '../../common/head.php';
    $obj_common->head();?>
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
                    <h3><i class="fa fa-angle-right"></i> Order Pick</h3>
                    <div style="margin-bottom:100px;">
                        <div class="form-group">
                            <div class="col-sm-3">
                                <input id="inputOrdNo" type="text" class="keyboard keyboard-numpad form-control" placeholder="Order #">
                            </div>
                            <div class="col-sm-2">
                                <button id="btnSearch" type="button" class="btn btn-default">Search</button>
                            </div>
                            <div class="col-sm-7">
                                &nbsp;
                            </div>
                            <div class="col-sm-12" style="margin-top:40px;">
                                <div class=" panel panel-primary">
                                    <div class="panel-body">
                                        <p>Customer: <label id="lblBillName"></label></p>
                                        <p>Order #: <label id="lblOrdNo"></label></p>
                                        <p>
                                            <a href="" id="btnStart" class="btn btn-primary btn-lg disabled" role="button">Start</a> 
                                            <a href="" id="btnEdit" class="btn btn-primary btn-lg disabled" role="button">Edit</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
		        </section><! --/wrapper -->


            </section><!-- /MAIN CONTENT -->

            <!-- Modal -->
            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                  </div>
                  <div class="modal-body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>

            <!--FOOTER-->
            <?php $obj_common->footer();?>
        
        </section>
        <!--JAVASCRIPT GENERAL-->
        <?php $obj_common->script();?>
        <script>

            $('#inputOrdNo').keyboard({type:'numpad'});
            
            $('#inputOrdNo').keypress(function(e){
                var tecla='';
                tecla = (document.all) ? e.keyCode : e.which;
                if (tecla==13){
                   $( "#btnSearch" ).click();
                }
            });

            $( "#btnSearch" ).click(function() {
                opc = 'searchOrder';
                activa_preloader();
                $.post("../controllers/<?php echo $controller;?>",
                    {
                        "opc":opc,
                        "OrdNo":$('#inputOrdNo').val()
                    },
                    function(data){
                        if(data.mss == '1'){
                            $('#lblOrdNo').html(data.OrdNo);
                            $('#lblBillName').html(data.BillName);
                            if (data.option == 'start'){
                                $('#btnStart').attr('href', 
                                    'orderPickEdit.php?r='+data.OrdID
                                    +'&BillName='+data.BillName
                                    +'&OrdNo='+data.OrdNo
                                    +'&option='+data.option);
                                $('#btnStart').removeClass('disabled');
                                $('#btnEdit').addClass('disabled');
                            }else{
                                $('#btnEdit').attr('href', 
                                    'orderPickEdit.php?r='+data.OrdID
                                    +'&BillName='+data.BillName
                                    +'&OrdNo='+data.OrdNo
                                    +'&option='+data.option);
                                $('#btnStart').addClass('disabled');
                                $('#btnEdit').removeClass('disabled');
                            }                            
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
        </script>
        <!--END SCRIPT-->
    </body>
</html>
<?php
    