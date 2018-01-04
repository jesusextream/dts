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
    $connectionInfo = array( "Databases"=>$BD, "UID"=>"roy", "PWD"=>"ry2016");
    $conn = sqlsrv_connect( $serverName, $connectionInfo);

    if (!$conn) {
      $error = 1;
    }

    //init CategoryList
    $qwery = "SELECT co_lin AS id, lin_des AS name FROM lin_art";
    $resultCategoryList = sqlsrv_query($conn, $qwery);
    if(!$resultCategoryList) {
      $error = 1;
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
      $error = 1;
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
    <!-- Switchery -->
    <link href="../../vendors/switchery/dist/switchery.min.css" rel="stylesheet">

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
                        <div class="col-md-1 control-group"><input id="chechk"  type="checkbox" checked="checked"></div>                            
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

                        <div style=" margin-top: -20px;" class="col-md-12 control-group">
                        <div class="ln_solid"></div>
                        </div>
                        <div class="col-md-6 control-group">
                          <label style=" margin-top: 0px;" class="control-label col-md-4 col-sm-4 col-xs-12" for="name">Search in orden <span class="required">*</span></label>
                          <div class="col-md-8 col-xs-12">
                            <input id="codigoArt" name="codigoArt" type="text" style=" margin-top: 0px;" class="form-control">
                          </div>
                        </div>
                        <div style=" margin-top: -20px;" class="col-md-12 control-group">
                        <div class="ln_solid"></div>
                        </div>

                        <div class="control-group">
                          <div class="col-md-11 col-md-offset-1">
                            <button type="submit" class="btn btn-primary">Cancel</button>
                            <button id="btnSend2" type="submit" class="btn btn-success">Submit</button>
                          </div>
                        </div>


<!--<table id="example" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Office</th>
                <th>Age</th>
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Office</th>
                <th>Age</th>
                <th>Start date</th>
                <th>Salary</th>
            </tr>
        </tfoot>
        <tbody>
            <tr>
                <td>Tiger Nixon</td>
                <td>System Architect</td>
                <td>Edinburgh</td>
                <td>61</td>
                <td>2011/04/25</td>
                <td>$320,800</td>
            </tr>
            <tr>
                <td>Garrett Winters</td>
                <td>Accountant</td>
                <td>Tokyo</td>
                <td>63</td>
                <td>2011/07/25</td>
                <td>$170,750</td>
            </tr>
            <tr>
                <td>Ashton Cox</td>
                <td>Junior Technical Author</td>
                <td>San Francisco</td>
                <td>66</td>
                <td>2009/01/12</td>
                <td>$86,000</td>
            </tr>
            <tr>
                <td>Cedric Kelly</td>
                <td>Senior Javascript Developer</td>
                <td>Edinburgh</td>
                <td>22</td>
                <td>2012/03/29</td>
                <td>$433,060</td>
            </tr>
            <tr>
                <td>Airi Satou</td>
                <td>Accountant</td>
                <td>Tokyo</td>
                <td>33</td>
                <td>2008/11/28</td>
                <td>$162,700</td>
            </tr>
            <tr>
                <td>Brielle Williamson</td>
                <td>Integration Specialist</td>
                <td>New York</td>
                <td>61</td>
                <td>2012/12/02</td>
                <td>$372,000</td>
            </tr>
            <tr>
                <td>Herrod Chandler</td>
                <td>Sales Assistant</td>
                <td>San Francisco</td>
                <td>59</td>
                <td>2012/08/06</td>
                <td>$137,500</td>
            </tr>
            <tr>
                <td>Rhona Davidson</td>
                <td>Integration Specialist</td>
                <td>Tokyo</td>
                <td>55</td>
                <td>2010/10/14</td>
                <td>$327,900</td>
            </tr>
            <tr>
                <td>Colleen Hurst</td>
                <td>Javascript Developer</td>
                <td>San Francisco</td>
                <td>39</td>
                <td>2009/09/15</td>
                <td>$205,500</td>
            </tr>
            <tr>
                <td>Sonya Frost</td>
                <td>Software Engineer</td>
                <td>Edinburgh</td>
                <td>23</td>
                <td>2008/12/13</td>
                <td>$103,600</td>
            </tr>
            <tr>
                <td>Jena Gaines</td>
                <td>Office Manager</td>
                <td>London</td>
                <td>30</td>
                <td>2008/12/19</td>
                <td>$90,560</td>
            </tr>
            <tr>
                <td>Quinn Flynn</td>
                <td>Support Lead</td>
                <td>Edinburgh</td>
                <td>22</td>
                <td>2013/03/03</td>
                <td>$342,000</td>
            </tr>
            <tr>
                <td>Charde Marshall</td>
                <td>Regional Director</td>
                <td>San Francisco</td>
                <td>36</td>
                <td>2008/10/16</td>
                <td>$470,600</td>
            </tr>
            <tr>
                <td>Haley Kennedy</td>
                <td>Senior Marketing Designer</td>
                <td>London</td>
                <td>43</td>
                <td>2012/12/18</td>
                <td>$313,500</td>
            </tr>
            <tr>
                <td>Tatyana Fitzpatrick</td>
                <td>Regional Director</td>
                <td>London</td>
                <td>19</td>
                <td>2010/03/17</td>
                <td>$385,750</td>
            </tr>
            <tr>
                <td>Michael Silva</td>
                <td>Marketing Designer</td>
                <td>London</td>
                <td>66</td>
                <td>2012/11/27</td>
                <td>$198,500</td>
            </tr>
            <tr>
                <td>Paul Byrd</td>
                <td>Chief Financial Officer (CFO)</td>
                <td>New York</td>
                <td>64</td>
                <td>2010/06/09</td>
                <td>$725,000</td>
            </tr>
            <tr>
                <td>Gloria Little</td>
                <td>Systems Administrator</td>
                <td>New York</td>
                <td>59</td>
                <td>2009/04/10</td>
                <td>$237,500</td>
            </tr>
            <tr>
                <td>Bradley Greer</td>
                <td>Software Engineer</td>
                <td>London</td>
                <td>41</td>
                <td>2012/10/13</td>
                <td>$132,000</td>
            </tr>
            <tr>
                <td>Dai Rios</td>
                <td>Personnel Lead</td>
                <td>Edinburgh</td>
                <td>35</td>
                <td>2012/09/26</td>
                <td>$217,500</td>
            </tr>
            <tr>
                <td>Jenette Caldwell</td>
                <td>Development Lead</td>
                <td>New York</td>
                <td>30</td>
                <td>2011/09/03</td>
                <td>$345,000</td>
            </tr>
            <tr>
                <td>Yuri Berry</td>
                <td>Chief Marketing Officer (CMO)</td>
                <td>New York</td>
                <td>40</td>
                <td>2009/06/25</td>
                <td>$675,000</td>
            </tr>
            <tr>
                <td>Caesar Vance</td>
                <td>Pre-Sales Support</td>
                <td>New York</td>
                <td>21</td>
                <td>2011/12/12</td>
                <td>$106,450</td>
            </tr>
            <tr>
                <td>Doris Wilder</td>
                <td>Sales Assistant</td>
                <td>Sidney</td>
                <td>23</td>
                <td>2010/09/20</td>
                <td>$85,600</td>
            </tr>
            <tr>
                <td>Angelica Ramos</td>
                <td>Chief Executive Officer (CEO)</td>
                <td>London</td>
                <td>47</td>
                <td>2009/10/09</td>
                <td>$1,200,000</td>
            </tr>
            <tr>
                <td>Gavin Joyce</td>
                <td>Developer</td>
                <td>Edinburgh</td>
                <td>42</td>
                <td>2010/12/22</td>
                <td>$92,575</td>
            </tr>
            <tr>
                <td>Jennifer Chang</td>
                <td>Regional Director</td>
                <td>Singapore</td>
                <td>28</td>
                <td>2010/11/14</td>
                <td>$357,650</td>
            </tr>
            <tr>
                <td>Brenden Wagner</td>
                <td>Software Engineer</td>
                <td>San Francisco</td>
                <td>28</td>
                <td>2011/06/07</td>
                <td>$206,850</td>
            </tr>
            <tr>
                <td>Fiona Green</td>
                <td>Chief Operating Officer (COO)</td>
                <td>San Francisco</td>
                <td>48</td>
                <td>2010/03/11</td>
                <td>$850,000</td>
            </tr>
            <tr>
                <td>Shou Itou</td>
                <td>Regional Marketing</td>
                <td>Tokyo</td>
                <td>20</td>
                <td>2011/08/14</td>
                <td>$163,000</td>
            </tr>
            <tr>
                <td>Michelle House</td>
                <td>Integration Specialist</td>
                <td>Sidney</td>
                <td>37</td>
                <td>2011/06/02</td>
                <td>$95,400</td>
            </tr>
            <tr>
                <td>Suki Burks</td>
                <td>Developer</td>
                <td>London</td>
                <td>53</td>
                <td>2009/10/22</td>
                <td>$114,500</td>
            </tr>
            <tr>
                <td>Prescott Bartlett</td>
                <td>Technical Author</td>
                <td>London</td>
                <td>27</td>
                <td>2011/05/07</td>
                <td>$145,000</td>
            </tr>
            <tr>
                <td>Gavin Cortez</td>
                <td>Team Leader</td>
                <td>San Francisco</td>
                <td>22</td>
                <td>2008/10/26</td>
                <td>$235,500</td>
            </tr>
            <tr>
                <td>Martena Mccray</td>
                <td>Post-Sales support</td>
                <td>Edinburgh</td>
                <td>46</td>
                <td>2011/03/09</td>
                <td>$324,050</td>
            </tr>
            <tr>
                <td>Unity Butler</td>
                <td>Marketing Designer</td>
                <td>San Francisco</td>
                <td>47</td>
                <td>2009/12/09</td>
                <td>$85,675</td>
            </tr>
            <tr>
                <td>Howard Hatfield</td>
                <td>Office Manager</td>
                <td>San Francisco</td>
                <td>51</td>
                <td>2008/12/16</td>
                <td>$164,500</td>
            </tr>
            <tr>
                <td>Hope Fuentes</td>
                <td>Secretary</td>
                <td>San Francisco</td>
                <td>41</td>
                <td>2010/02/12</td>
                <td>$109,850</td>
            </tr>
            <tr>
                <td>Vivian Harrell</td>
                <td>Financial Controller</td>
                <td>San Francisco</td>
                <td>62</td>
                <td>2009/02/14</td>
                <td>$452,500</td>
            </tr>
            <tr>
                <td>Timothy Mooney</td>
                <td>Office Manager</td>
                <td>London</td>
                <td>37</td>
                <td>2008/12/11</td>
                <td>$136,200</td>
            </tr>
            <tr>
                <td>Jackson Bradshaw</td>
                <td>Director</td>
                <td>New York</td>
                <td>65</td>
                <td>2008/09/26</td>
                <td>$645,750</td>
            </tr>
            <tr>
                <td>Olivia Liang</td>
                <td>Support Engineer</td>
                <td>Singapore</td>
                <td>64</td>
                <td>2011/02/03</td>
                <td>$234,500</td>
            </tr>
            <tr>
                <td>Bruno Nash</td>
                <td>Software Engineer</td>
                <td>London</td>
                <td>38</td>
                <td>2011/05/03</td>
                <td>$163,500</td>
            </tr>
            <tr>
                <td>Sakura Yamamoto</td>
                <td>Support Engineer</td>
                <td>Tokyo</td>
                <td>37</td>
                <td>2009/08/19</td>
                <td>$139,575</td>
            </tr>
            <tr>
                <td>Thor Walton</td>
                <td>Developer</td>
                <td>New York</td>
                <td>61</td>
                <td>2013/08/11</td>
                <td>$98,540</td>
            </tr>
            <tr>
                <td>Finn Camacho</td>
                <td>Support Engineer</td>
                <td>San Francisco</td>
                <td>47</td>
                <td>2009/07/07</td>
                <td>$87,500</td>
            </tr>
            <tr>
                <td>Serge Baldwin</td>
                <td>Data Coordinator</td>
                <td>Singapore</td>
                <td>64</td>
                <td>2012/04/09</td>
                <td>$138,575</td>
            </tr>
            <tr>
                <td>Zenaida Frank</td>
                <td>Software Engineer</td>
                <td>New York</td>
                <td>63</td>
                <td>2010/01/04</td>
                <td>$125,250</td>
            </tr>
            <tr>
                <td>Zorita Serrano</td>
                <td>Software Engineer</td>
                <td>San Francisco</td>
                <td>56</td>
                <td>2012/06/01</td>
                <td>$115,000</td>
            </tr>
            <tr>
                <td>Jennifer Acosta</td>
                <td>Junior Javascript Developer</td>
                <td>Edinburgh</td>
                <td>43</td>
                <td>2013/02/01</td>
                <td>$75,650</td>
            </tr>
            <tr>
                <td>Cara Stevens</td>
                <td>Sales Assistant</td>
                <td>New York</td>
                <td>46</td>
                <td>2011/12/06</td>
                <td>$145,600</td>
            </tr>
            <tr>
                <td>Hermione Butler</td>
                <td>Regional Director</td>
                <td>London</td>
                <td>47</td>
                <td>2011/03/21</td>
                <td>$356,250</td>
            </tr>
            <tr>
                <td>Lael Greer</td>
                <td>Systems Administrator</td>
                <td>London</td>
                <td>21</td>
                <td>2009/02/27</td>
                <td>$103,500</td>
            </tr>
            <tr>
                <td>Jonas Alexander</td>
                <td>Developer</td>
                <td>San Francisco</td>
                <td>30</td>
                <td>2010/07/14</td>
                <td>$86,500</td>
            </tr>
            <tr>
                <td>Shad Decker</td>
                <td>Regional Director</td>
                <td>Edinburgh</td>
                <td>51</td>
                <td>2008/11/13</td>
                <td>$183,000</td>
            </tr>
            <tr>
                <td>Michael Bruce</td>
                <td>Javascript Developer</td>
                <td>Singapore</td>
                <td>29</td>
                <td>2011/06/27</td>
                <td>$183,000</td>
            </tr>
            <tr>
                <td>Donna Snider</td>
                <td>Customer Support</td>
                <td>New York</td>
                <td>27</td>
                <td>2011/01/25</td>
                <td>$112,000</td>
            </tr>
        </tbody>
    </table>-->


                      <!--</form>-->

                    </div>

                  </div>
                </div>
                <!-- /form datepicker -->


              </div>

              <!--div id="datatableDetailsinvetory" class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Detail <small>find</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" style=" height: 440px; overflow: scroll;">
                    <table id="datatableDetails" class="table table-striped table-bordered" style=" width: 98%;">
                      <thead>
                        <tr>
                          <th>Sel</th>
                          <th>PartNo</th>
                          <th>Nombre proveedor</th>
                          <th>Fecha orden</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                          <th>Description</th>
                          <th>Origen</th>
                          <th>QtyAvi</th>
                          <th>SalesPrice</th>
                          <th>ModRef</th>
                          <th>QtyOrder</th>
                          <th>QtySales</th>
                          <th>Sug</th>
                        </tr>
                      </thead>
                      <tbody id="catalogo_articulo_list_busca"></tbody>
                    </table>
                  </div>
                  <br>
                  <div class="col-md-4" style="margin:10px 0px 0px 0px;">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12">
                      <input id="inputSelectedAll" type="checkbox" class="js-switch" /> selected all
                    </label>
                  </div>
                  <div class="col-md-4" style="margin:10px 0px 0px 0px;">
                    <label id="lblTotalRow" class="control-label"></label>
                  </div>
                  <div class="col-md-4" style="margin:10px 0px 0px 0px;"><button type="button" class="btn btn-default" onclick="cargar_articulo();">Load</button></div>
                </div>
              </div!-->

              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Detail <small>find</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                      <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content" ><p>
                    Show: 
                    <select id="table-filter">
                    <option value="">All</option>
                    <option>Abierta</option>
                    <option>Facturada</option>
                    </select>
                    </p>
                    <table id="datatableDetails2" class="table table-striped table-bordered"">
                      <thead>
                        <tr>
                          <th>Sel</th>
                          <th>PartNo</th>
                          <th>Nombre proveedor</th>
                          <th>Descripcion</th>
                          <th>PO Num</th>
                          <th>Status</th>
                          <th>Fecha orden</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                        </tr>
                      </thead>
                      <tbody id="catalogo_articulo_list_busca"></tbody>
                    </table>
                  </div>
                  <br>
                  <!--div class="col-md-4" style="margin:10px 0px 0px 0px;">
                    <label class="control-label col-md-4 col-sm-4 col-xs-12">
                      <input id="inputSelectedAllOrdenes" type="checkbox" class="js-switch" /> selected all
                    </label>
                  </div>
                  <div class="col-md-4" style="margin:10px 0px 0px 0px;">
                    <label id="lblTotalRow" class="control-label"></label>
                  </div>
                  <div class="col-md-4" style="margin:10px 0px 0px 0px;"><button type="button" class="btn btn-default" onclick="cargar_articuloOrdenes();">Load</button>
                  </div-->
                </div>
              </div>

                <!-- Modal confirmacion -->
                <div class="modal fade" id="ModalError" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">Posibles problemas</h4>
                            </div>
                            <div class="modal-body">
                                • Conexión de internet inestable.</br>
                                • Conexión inestable o falla de suministro a internet en el servidor de Venezuela.</br>
                                • Por favor cierre todo y vuelva a intentarlo mas tarde
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
                            </div>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <!-- /.modal confirmacion -->
              <!--div id="datatableExport" class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Master</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                     <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatableDetailsExport" class="table table-striped table-bordered">
                       <thead>
                        <tr>
                          <th>PartNo</th>
                          <th>Nombre proveedor</th>
                          <th>Fecha orden</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                          <th>Description</th>
                          <th>Origen</th>
                          <th>ModRef</th>
                          <th>QtyAvi</th>
                          <th>SalesPrice</th>>
                          <th>QtyOrder</th>
                          <th>QtySales</th>
                          <th>Suggested</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div-->
              <!--div  class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Master</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                     <li><a class="close-link"><i class="fa fa-close"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <table id="datatableDetailsExportOrden" class="table table-striped table-bordered">
                       <thead>
                        <tr>
                          <th>PartNo</th>
                          <th>Nombre proveedor</th>
                          <th>Fecha orden</th>
                          <th>Cantidad</th>
                          <th>Precio</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div-->
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
    <!--PRELOADER-->
    <script src="../../assets/js/preloader.js" type="text/javascript"></script>
    <!-- Switchery -->
    <script src="../../vendors/switchery/dist/switchery.min.js"></script>
    <!-- jQuery autocomplete -->
    <script src="../../vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
   
    <!-- Custom Theme Scripts -->
    <script src="../../build/js/custom.js?v=3"></script>

    <script>
      $(document).ready(function(){

        //$('#dataTableOrdenes').hide();
        //$('#datatableExportOrden').hide();
        var table = $("#datatableDetails").DataTable({
          searching: false,
          paging:false,
          columnDefs: [
            {targets:0, width: "4%"},
            {targets:[3,5,6,7,8,9,10,11], width: "5%", orderable:false}
          ],
          responsive: true
        });
        var table2 = $("#datatableDetails2").DataTable({
          paging:false,
            scrollY:"500px",
            scrollCollapse:true,
            dom: "Bfrtip",
            dom: "lrtip",
          columnDefs: [
            {targets:0, width: "4%"},
            {targets:[1,2,3,4,5,6,7,8], width: "5%"}
          ],
                order: [[ 1, "asc" ]],
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

    $('#table-filter').on('change', function(){
       table2.search(this.value).draw();   
    });

    /*$('#example').DataTable( {
          paging:false,
            scrollY:"500px",
            scrollCollapse:true,
            order: [[ 1, "asc" ]],
            dom: "lrtip",
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
          responsive: true,
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    } );*/
        $('#inputSelectedAll').change(function(){
            var cells = table.cells( ).nodes();
            $( cells ).find(':checkbox').prop('checked', $(this).is(':checked'));
        });

        $('#inputSelectedAllOrdenes').change(function(){
            var cells = table2.cells( ).nodes();
            $( cells ).find(':checkbox').prop('checked', $(this).is(':checked'));
        });

        var toMmDdYy = function(input) {
            var ptrn = /(\d{4})\-(\d{2})\-(\d{2})/;
            if(!input || !input.match(ptrn)) {
                return null;
            }
            return input.replace(ptrn, '$2/$3/$1');
        };


        var tx = $('#datatableDetailsExportOrden').DataTable({
            scrollY:"200px",
            scrollCollapse:true,
            paging:false,
            dom: "Bfrtip",
            columnDefs: [
              {targets:0,visible: false},
              {targets:[0,1,2,3,4,5,6,7,8], width: "5%", orderable:false}
            ],
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
        var t = $('#datatableDetailsExport').DataTable({
            scrollY:"200px",
            scrollCollapse:true,
            paging:false,
            dom: "Bfrtip",
            columnDefs: [
              {targets:[3],visible: false},
              {targets:[2,4,5,6,7,8,9,10,11], width: "5%", orderable:false}
            ],
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
            lookupFilter: function (suggestion, originalQuery, queryLowerCase) {
              return suggestion.value.toLowerCase().indexOf(queryLowerCase) === 0;
            },
            onSelect: function (suggestion) {
              AutoCompleteSelectHandler(suggestion.data, suggestion.value);
            }
          });
         
        };

        init_autocomplete();

        //CARGA SELECT SUBCATEGORIA DE A PARTIR DE LA CATEGORIA
        function AutoCompleteSelectHandler(co_lin,lin_des) {
            $('inputHddCategory').val(co_lin);
            $("#divSubCategory").append('<input type="checkbox" id="catalogo_ch_'+co_lin+'" value="'+co_lin+'" checked> <label for="catalogo_ch_'+co_lin+'">'+lin_des+'</label> <br>');
        }

        //BUSCA ARTICULO
        $( "#btnSend" ).click(function() {
          var catalogo_categoria = '';
          var catalogo_subcategoria = '';
          var catalogo_ventaFrom = '';
          var catalogo_ventaTo = '';
          var catalogo_db = '';
          activa_preloader();

        //$('#dataTableOrdenes').hide();
        //$('#datatableExportOrden').hide();
        //$('#datatableDetailsinvetory').show();
        //$('#datatableExport').show();
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
          catalogo_db = $('#selBD').val();
          co_art = $('#inputDepartment').val();

          $.post("../controllers/<?php echo $controller;?>",{
            "opc":opc
            ,"catalogo_categoria":catalogo_categoria
            ,"catalogo_subcategoria":catalogo_subcategoria
            ,"catalogo_ventaFrom" : catalogo_ventaFrom
            ,"catalogo_ventaTo" : catalogo_ventaTo
            ,"catalogo_db" : catalogo_db
            ,"co_art" : co_art
          },function(data){
              if(data.mss === '1'){
                  $('#datatableDetails').dataTable().fnClearTable();
                  $.each( data.salida, function(i, row) {
                      //    QtyOrder >= QtySales :  Suggested = QtyOrder - QtyAvi
                      //    QtyOrder < QtySales : Suggested = QtySales - QtyAvi
                      QtyAvi = Math.floor(row.stock_act);
                      QtyOrder = Math.floor(row.pedidos);
                      QtySales = row.QtySales;

                      if(QtyOrder >= QtySales){                        
                        Suggested = QtyOrder - QtyAvi;                          
                      }else{
                        Suggested = QtySales - QtyAvi;
                      }
                      if( Suggested <= 0){ //Si se Ordeno/Vendio menos o igual a lo que hay en Stock no hay que sugerir nada 0
                        Suggested = 0;
                      }

                      table.row.add( [
                        "<th><input type='checkbox' id='catalogo_articulo_list_ch_"+row.co_art+"' value='"+row.co_art+"'></th>",
                        row.co_art,
                        row.art_des,
                        row.db,
                        QtyAvi,
                        row.ModRef,
                        QtyOrder,
                        QtySales,
                        row.QtyCustumers,
                        row.QtyInvoice,
                        row.QtyAviDTS,
                        row.QtyAviTex,
                        Suggested
                      ] );
                  });
                  table.sort().draw();
                  var totalRow = table.column(0).data().length;
                  [1].sort(table[this.key || 'asc']);
                  $('#lblTotalRow').html('Showing 1 to '+totalRow+' of '+totalRow+' entries');
                  //$('#datatableDetails tbody').append(html);
                  $("#divSubCategory").html('');
                  $('#inputCategory').val('');
                  
              }else{
                  console.log(data);
                  $('#datatableDetails tbody').html('');
                  data.mss = 0;
                if (data.mss == 0) {

                          $('#ModalError').modal('show');
                }
              }
              desactiva_preloader();
          },"json");

          //CARGA ARTICULOS AL CATALOGO
          cargar_articulo = function() {

            var cadena = '';
            ch_art = '';
            var cells = table.cells( ).nodes();

            var i = 1;
            $("input:checked", table.cells( ).nodes()).each(function(){
              var data = table.row($(this).parents('tr')).data();
              co_art = data[1];
              art_des = data[2];
              db = data[3];
              stock_act = data[4];
              ModRef = data[5];
              QtyOrder = data[6];
              QtySales = data[7];
              QtyCustumers = data[8];
              QtyInvoice = data[9];
              QtyAviDTS = data[10];
              QtyAviTex = data[11];
              Suggested = data[12];

              t.row.add( [
                co_art,
                art_des,
                db,
                ModRef,
                stock_act,
                QtyOrder,
                QtySales,
                QtyCustumers,
                QtyInvoice,
                QtyAviDTS,
                QtyAviTex,
                Suggested
              ] ).draw( false );

            });
          }
               
        });
        //BUSCA ARTICULO
        $( "#btnSend2" ).click(function() {
          var catalogo_categoria = '';
          var catalogo_subcategoria = '';
          var catalogo_ventaFrom = '';
          var catalogo_ventaTo = '';
          var catalogo_db = '';
          activa_preloader();

        //$('#datatableDetailsinvetory').hide();
        //$('#datatableExport').hide();
        //$('#dataTableOrdenes').show();

    
          opc = "ArtBusca";
          if ($("#chechk").is(':checked')) {           
          co_art = $('#codigoArt').val();       
          catalogo_ventaFrom = $('#inputFrom').val();
          catalogo_ventaTo = $('#inputTo').val();
          }else{
          co_art = $('#codigoArt').val();       
          catalogo_ventaFrom = '';
          catalogo_ventaTo = '';   
          }

         

          $.post("../controllers/<?php echo $controller;?>",{
            "opc":opc
            ,"catalogo_categoria":catalogo_categoria
            ,"catalogo_subcategoria":catalogo_subcategoria
            ,"catalogo_ventaFrom" : catalogo_ventaFrom
            ,"catalogo_ventaTo" : catalogo_ventaTo
            ,"catalogo_db" : catalogo_db
            ,"co_art" : co_art
          },function(data){
              if(data.mss === '1'){
                  $('#datatableDetails2').dataTable().fnClearTable();
                  $.each( data.salida, function(i, row) {
                      //    QtyOrder >= QtySales :  Suggested = QtyOrder - QtyAvi
                      //    QtyOrder < QtySales : Suggested = QtySales - QtyAvi
                      QtyAvi = Math.floor(row.stock_act);
                      QtyOrder = Math.floor(row.pedidos);
                      QtySales = row.QtySales;

                      if(QtyOrder >= QtySales){                        
                        Suggested = QtyOrder - QtyAvi;                          
                      }else{
                        Suggested = QtySales - QtyAvi;
                      }
                      if( Suggested <= 0){ //Si se Ordeno/Vendio menos o igual a lo que hay en Stock no hay que sugerir nada 0
                        Suggested = 0;
                      }

                      table2.row.add( [
                        "<th><input type='checkbox' id='catalogo_articulo_list_ch_"+row.co_art+"' value='"+row.co_art+"'></th>",
                        row.co_art,
                        row.Nombre_p,
                        row.descripcion,
                        row.PO_Num,
                        row.statusname,
                        row.F_orden,
                        row.Cantidad,
                        row.Precio
                      ] );
                  });
                  table2.sort().draw();
                  var totalRow = table2.column(0).data().length;
                  [1].sort(table[this.key || 'asc']);
                  $('#lblTotalRow').html('Showing 1 to '+totalRow+' of '+totalRow+' entries');
                  //$('#datatableDetails tbody').append(html);
                  $("#divSubCategory").html('');
                  $('#inputCategory').val('');
                  
              }else{
                  console.log(data);
                  $('#datatableDetails2 tbody').html('');
              }
              desactiva_preloader();
          },"json");

          //CARGA ARTICULOS AL CATALOGO
          cargar_articuloOrdenes = function() {

        //$('#datatableExportOrden').show();
            var cadena = '';
            ch_art = '';
            var cells = table2.cells( ).nodes();

            var i = 1;
            $("input:checked", table2.cells( ).nodes()).each(function(){
              var data = table2.row($(this).parents('tr')).data();
              co_art = data[1];
              Nombre_p = data[2];
              descripcion = data[3];
              PO_Num = data[4];
              statusname = data[5];
              F_orden = data[6];
              Cantidad = data[7];
              Precio = data[8];

              tx.row.add( [
                co_art,
                Nombre_p,
                descripcion,
                PO_Num,
                statusname,
                F_orden,
                Cantidad,
                Precio
              ] ).draw( false );

            });
          }
               
        });
    });
        var error='<?php echo$error;?>'
        if (error == 1) {

                  $('#ModalError').modal('show');
        }
  </script>

  <!--END SCRIPT-->

  </body>
</html>