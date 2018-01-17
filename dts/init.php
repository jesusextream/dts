<?php
    include '../../common/general.php';
    $obj_common = new common();
?>
<!DOCTYPE html>
<html lang="en">
  <?php $obj_common->head();?>

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
          <?php $obj_common->right_col();?>
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
    
    <!-- Custom Theme Scripts -->
    <script src="../../build/js/custom.min.js"></script>
  </body>
</html>
