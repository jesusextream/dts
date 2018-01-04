
$(document).ready(function() {
    var parser = document.createElement('a');
    parser.href = document.url;
    intuit.ipp.anywhere.setup({
        menuProxy: '',
        //grantUrl: 'http://'+parser.hostname+':8001/PHPOAuthSample/oauth.php?start=t' 
        grantUrl: 'http://'+parser.hostname+':8001/facturas/quickbooks/oauth/oauth.php?start=t' 
        // outside runnable you can point directly to the oauth.php page
    });


//    alert('recaraga');

});



function Disconnect(parameter){
window.location.href = "http://localhost:8001/facturas/quickbooks/oauth/Disconnect.php";
}

function Reconnect(parameter){
window.location.href = "http://localhost:8001/facturas/quickbooks/oauth/Reconnect.php";
}