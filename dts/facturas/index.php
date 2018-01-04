<?php if (isset($_GET['pag'])) $pag = $_GET['pag']; ?>
        <?php require_once 'constants.php'; ?>
<html>
    
    <head>
        <?php require_once 'views/head.html'; ?>
        
        <?php if (!isset($pag) || strcmp($pag,_MAIN_) == 0){ ?>
        <script src="js/main.js"></script>
        <script src="js/main-quickbooks.js"></script>

<?php } ?>
<?php if ( isset($pag) && strcmp($pag,_COMPANIES_) == 0){ ?>
        <script src="js/companies.js"></script>

<?php } ?>
<?php if ( isset($pag) && strcmp($pag,_CUSTOMERS_) == 0){ ?>
        <script src="js/clientes.js"></script>

<?php } ?>

<?php if ( isset($pag) && strcmp($pag,_QUICKBOOKS_) == 0){ ?>
        <script src="js/main-quickbooks.js"></script>

<?php } ?>

<?php if ( isset($pag) && strcmp($pag,_ACCEPTED_) == 0){ ?>
        <script src="js/testGetDataQBO.js"></script>

<?php } ?>
        
        <?php if ( isset($pag) && strcmp($pag,_PROFIT_) == 0){ ?>
        <script src="js/profit.js"></script>

<?php } ?>
    </head>
    <body>

        <?php require_once 'views/nav.html'; ?>
        
        <?php 
        if (!isset($pag) || strcmp($pag,_MAIN_) == 0){
        
            require_once 'views/main.html'; 
        
        } else if (strcmp($pag,_COMPANIES_) == 0){
            
            require_once 'views/companies.html'; 
            
        } else if (strcmp($pag,_CUSTOMERS_) == 0){
            
            require_once 'views/clientes.html'; 
            
        } else if (strcmp($pag,_QUICKBOOKS_) == 0){
            
            require_once 'views/quickbooks.html'; 
            
        } else if (strcmp($pag,_ACCEPTED_) == 0){
            
            require_once 'views/testGetDataQBO.html'; 
            
        } else if (strcmp($pag,_PROFIT_) == 0){
            
            require_once 'views/profit.html'; 
            
        } else {
             require_once 'views/main.html'; 
        } 
        
        ?>
      
<?php require_once 'views/modal.html'; ?>
    </body>
</html>