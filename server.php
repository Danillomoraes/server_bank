<!DOCTYPE html>
<html>
  <body>
         
        <?php 
    
          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);
    
          function sendReqp ($post, $url, $header) {
            
                  $opts = array('http'=> array(
                                'method' => 'POST',
                                'header' => $header,
                                'content' => $post                          
                                )
                  );

              $context = stream_context_create($opts, ['verify_peer' => false]);

              $response = file_get_contents($url, false, $context);            
            
              return $response;
          };
    
         function sendReqg ($url, $header) {
           
              $opts = array ('http'=> array(
                             'method' => 'GET',
                             'header' => $header                
                             )
              );
           
               
              $context = stream_context_create($opts, ['verify_peer' => false]);

              $response = file_get_contents($url, false, $context);            
            
              return $response;           
         }
    
          $post='{"name":"Nubank","uri":"https://conta.nubank.com.br"}';
          $url = 'https://prod-auth.nubank.com.br/api/registration';
          $header = 'Content-type: application/json';
    
          $jsonresponse = sendReqp($post, $url, $header);
    
          echo $jsonresponse;
    
          echo "<br>-------------------------------------------------------------------- <br>";
    
          $json = json_decode ($jsonresponse);
          $clientid = $json->client_id;      
          $clientsecret = $json->client_secret;
    
          $login = $_POST["cpf"];
          $pass = $_POST["password"];
            
          $post = '{"grant_type":"password","username":"'.$login.'","password":"'.$pass.'","client_id":"'.$clientid.'","client_secret":"'.$clientsecret.'","nonce":"NOT-RANDOM-YET"}';
          $url = 'https://prod-auth.nubank.com.br/api/token';   
          
          $jsonresponse = sendReqp($post, $url, $header);
    
          echo $jsonresponse;
    
          echo "<br>-------------------------------------------------------------------- <br>";
    
          
          $json = json_decode($jsonresponse);
          $token = $json->access_token;
    
          $header = "Authorization: Bearer ".$token;
          $url = 'https://prod-customers.nubank.com.br/api/customers';
          $post = "";
    
          $jsonresponse = sendReqg($url, $header);
    
          echo $jsonresponse;
    
          echo "<br>-------------------------------------------------------------------- <br>";
    
    
          $json = json_decode($jsonresponse);
          $customerjson = $json->customer;
          $customerid = $customerjson->id;
          
          
          $header= "Authorization: Bearer ".$token;
          $url = "https://prod-credit-card-accounts.nubank.com.br/api/".$customerid."/accounts";
          
          $jsonresponse = sendReqg($url, $header);
    
          echo $jsonresponse;
          
          echo "<br>";
      
          $json = json_decode($jsonresponse); // ja consigo puxar daqui: dia_pagamento, limite_disponivel, faturas_futuras, fatura_atual. classe balances.
          $accountjson = $json->accounts[0];
          $accountid = $accountjson->id;
    
          //$url = "https://prod-s0-feed.nubank.com.br/api/accounts/58f8113c-a361-4d4f-8402-7a7a302972d3/transactions"
          $url = "https://prod-s0-feed.nubank.com.br/api/accounts/".$accountid."/transactions";
  
          $jsonresponse = sendReqg($url, $header);
          
          echo $jsonresponse;
    
          $finalresponse = $jsonresponse;
    
        ?>
        
        <p id="resp"></p>
    
        <script src="konklonejson/json/assets/site.js"></script>
        <script src="konklonejson/json/assets/highlight.pack.js"></script> 
    
        <script>
        
        /*  function doCSV(json) {
                  // 1) find the primary array to iterate over
                  // 2) for each item in that array, recursively flatten it into a tabular object
                  // 3) turn that tabular object into a CSV row using jquery-csv
                  var inArray = arrayFrom(json);

                  var outArray = [];
                  for (var row in inArray)
                      outArray[outArray.length] = parse_object(inArray[row]);

                  //$("span.rows.count").text("" + outArray.length);

                  var csv = $.csv.fromObjects(outArray);
                  //excerpt and render first few rows
                  //renderCSV(outArray.slice(0, excerptRows));
                  //showCSV(true);

                  // if there's more we're not showing, add a link to show all
                  //if (outArray.length > excerptRows)
                    //$(".show-render-all").show();

                  // show raw data if people really want it
                  //$(".csv textarea").val(csv);

                  // download link to entire CSV as data
                  // thanks to https://jsfiddle.net/terryyounghk/KPEGU/
                  // and https://stackoverflow.com/questions/14964035/how-to-export-javascript-array-info-to-csv-on-client-side
                  //var uri = "data:text/csv;charset=utf-8," + encodeURIComponent(csv);
                  //$(".csv a.download").attr("href", uri);
                  
                  return csv;
            
                  }
          
          var json =  <?php /* echo json_encode($finalresponse);*/ ?>;
          
          var csv = doCSV(json);
          
          document.getElementbyId("resp").innerHTML = csv; */
          
          
          
        </script> 
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
  </body>
</html>