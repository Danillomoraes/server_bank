<?php   

date_default_timezone_set('Europe/London');

        function errHandle($errNo, $errStr, $errFile, $errLine) {
              $msg = "$errStr in $errFile on line $errLine";
              if ($errNo == E_NOTICE || $errNo == E_WARNING) {
                  throw new ErrorException($msg, $errNo);
              } else {
                  echo $msg;
              }
        }


set_error_handler('errHandle');
        
        function oldfeed($conn) {
                   
        echo "entrando while  ".PHP_EOL;
        
        //while (true) {  
        $header=[];
        $pairs = array();
        $polo = "https://poloniex.com/public?command=returnTicker";
        $response = request($polo, $header);
        $time = time();
                  
        $array = json_decode($response);

        $i = 0;      
        
        foreach ($array as $key=>$value) {
          
          $pairs[$i] = $key;
          $i++;
        }

        $i = 0;

        foreach ($array as $pair) {
            
            $sql = "insert into returnticker (last, pair, hora, chang, volume) values ('".$pair->last."','".$pairs[$i]."','".$time."','".$pair->percentChange."','".$pair->baseVolume."')";
            
            if ($conn->query($sql) === TRUE) {
              $msg = "Record created successfully";      
              //appendlog($msg, "poloniex.log");   //echo "New record created successfully";
            } else {
              echo $conn->error;      
              //appendlog($msg, "poloniex.log");
                    //echo "Error: " . $sql . "<br>" . $conn->error;
            }         
            
            $i++;
        } 
          
                 
        }          


        function newfeed($conn) {
          $date = date('Y-m-d H:i:s');
          echo "newfeed".$date.PHP_EOL;
          $sql = "select pair from pair_last_change";
          $result = $conn->query($sql);

          $timer = 0;          
          
          while ($row = $result->fetch_assoc()) {
            
           // echo $row['pair'].PHP_EOL;
            
            $sql = "select hora from returnticker where pair= '".$row['pair']."' order by hora desc";
            $res = $conn->query($sql);
            $linha = $res->fetch_assoc();                      
          //  echo $linha['hora'];
            
            $http = "https://poloniex.com/public?command=returnTradeHistory&currencyPair=".$row['pair']."&start=".$linha['hora']."&end=".time();
            $header=[];
            
            $response = request($http, $header);            
            usleep(300);
            
          //  echo print_r($response);
            
            $json = json_decode($response);
            $ticker = [];
            
        //    echo print_r($json);
            
            foreach ($json as $obj) {
              
              $sql = "insert into returnticker (pair, last, hora, volume, chang) values ('".$row['pair']."',".$obj->rate.", ".strtotime($obj->date).", 0, 0)";
              
              if($conn->query($sql)){
                
              }else {
                echo $conn->error;
                
              }                            
              
            }                        
            
          }
          
        }

        function clrdatabase ($conn) {
          $date = date('Y-m-d H:i:s');
          echo "clrdatabase ".$date.PHP_EOL;
          $time = time()-(3*60*60);
          $sql = "delete from returnticker where hora <= ".$time;
          
          if(!$conn->query($sql)){
            throw new exception ($conn->error);
          }                
          
        }

        function request ($http, $header) {
          
               $opts = array ('http'=> array(
                             'method' => 'GET',
                             'header' => $header                
                             )
              );
          
              $context = stream_context_create($opts, ['verify_peer' => false]);

              $response = file_get_contents($http, false, $context);            
            
              return $response;
          
        }

        function trouxa ($conn) {
          
        $polo = "https://poloniex.com/public?command=returnTicker";
        $header = [];
        $response = request($polo, $header);
        $time = time();
        $pairs = array();  
        
        sleep(5);
          
        $array = json_decode($response);

        $i = 0;      
          
        foreach ($array as $key=>$value) {
          
          $pairs[$i] = $key;
          $i++;
        }

        $i = 0;

        foreach ($array as $pair) {
            
            $sql = "insert into pair_last_change (pair, last, um, cinco, dez, trinta, hora) values ('".$pairs[$i]."', 0.0000001, 0.0000001, 0.0000001, 0.0000001, 0.0000001, 0.0000001)";
            
            if ($conn->query($sql)) {
              $msg = "Record created successfully";      
              //appendlog($msg, "poloniex.log");   //echo "New record created successfully";
            } else {
              throw new exception ($conn->error);
              $msg = "ERROR: Insert returnticker on database";      
              //appendlog($msg, "poloniex.log");
                    //echo "Error: " . $sql . "<br>" . $conn->error;
            }
          $sql = "insert into pair_volume_change (pair, volume, um, cinco, dez, trinta, hora) values ('".$pairs[$i]."', 0.0000001, 0.0000001, 0.0000001, 0.0000001, 0.0000001, 0.0000001)";
            
            if ($conn->query($sql)) {
              $msg = "Record created successfully";      
              //appendlog($msg, "poloniex.log");   //echo "New record created successfully";
            } else {
              throw new exception ($conn->error);
              $msg = "ERROR: Insert returnticker on database";      
              //appendlog($msg, "poloniex.log");
                    //echo "Error: " . $sql . "<br>" . $conn->error;
            }
            
            $i++;
        }
          
          
        }

        function appendlog ($msg, $file) {
          $date = date('Y-m-d H:i:s');
          $nmsg = $date." -- ".$msg." --- ".PHP_EOL;
          file_put_contents($file, $nmsg, FILE_APPEND);
     
        }

        function calc_valorizacao ($conn) {
              $date = date('Y-m-d H:i:s');
              echo "calc_valorizacao  ".$date.PHP_EOL;
          
              $sql = "select pair from pair_last_change";
              $result = $conn->query($sql);
              
              while ($row = $result->fetch_assoc()) {
                
                
                
                
              }
              
          
          
          
              /* $sql = "Select * from returnticker";              
          
          
              $result = $conn->query($sql);
              $pair = [];
              while ($row = $result->fetch_assoc()) {
                   $s = array_search($row["pair"], $pair);
                    if ($s == false) {                       
                          array_push($pair, $row["pair"]);
                    }                
              }
          
              mysqli_data_seek($result, 0);
          
              $pair_last_change = array();
              $pair_volume_change = array();
          
              foreach ($pair as $pairs) {
                   $time = time() - (60*60);
                   //echo $time;
                   $sql = "select pair, last, hora, volume from returnticker where pair = '".$pairs."' and hora >= ".$time." order by hora desc";
                  
                   if ($result = $conn->query($sql)) {
                     
                   }else{
                     throw new Exception($conn->error." ".$time);
                   }
                   
                   $pair_data = array();
                    
                   while ($row = $result->fetch_assoc()) {
                         array_push ($pair_data, [$row['last'], $row['hora'], $row['volume']]);
                   }
                                    
                   //last                                    
                
                   $um=0;
                   $cinco=0;
                   $dez=0;
                   $trinta=0;
                   $hora =0;
                            
                   //volume                 
                                    
                   $umv=0;
                   $cincov=0;
                   $dezv=0;
                   $trintav=0;
                   $horav =0;                                    
                //echo "pair_data".PHP_EOL;
                //echo $pair_data[0][0].PHP_EOL;
                //echo $pair_data[0][2].PHP_EOL;
                
                //echo "linha".PHP_EOL;
                
                  foreach ($pair_data as $linha) {
                    $time = time();
                    //echo "time".$time.PHP_EOL;
                    //echo print_r($linha);
                    if ($linha[1]>=($time-(60))) { // $linha[1]<($time-(70))) {
                        $um = ($pair_data[0][0]/$linha[0]);
                        //$umv = ($pair_data[0][2]/$linha[2]);
                      }
                    if ($linha[1]>($time-(60*5))) { //and {//$linha[1]<($time-((60*5)+10))) {
                        $cinco = ($pair_data[0][0]/$linha[0]);
                        //$cincov = ($pair_data[0][2]/$linha[2]);
                      }
                    if ($linha[1]>($time-(60*10))) { //and $linha[1]<($time-((60*10)+10))) {
                        $dez = ($pair_data[0][0]/$linha[0]);
                        //$dezv = ($pair_data[0][2]/$linha[2]);
                      }
                    if ($linha[1]>($time-(60*30))) { //and $linha[1]<($time-((60*30)+10))) {
                        $trinta = ($pair_data[0][0]/$linha[0]);
                        //$trintav = ($pair_data[0][2]/$linha[2]);
                      }
                    if ($linha[1]>($time-(60*60))) { //and $linha[1]<($time-((60*60)+10))) {
                        $hora = ($pair_data[0][0]/$linha[0]);
                        //$horav = ($pair_data[0][2]/$linha[2]);
                      }
                  }
                  
                  array_push($pair_last_change, [$pairs, $linha[0], $um, $cinco, $dez, $trinta, $hora]);
                  array_push($pair_volume_change, [$pairs, $linha[2], $umv, $cincov, $dezv, $trintav, $horav]);
                
              }
          
                //echo print_r($pair_last_change);
                //echo print_r($pair_volume_change);
                
                   foreach ($pair_last_change as $row) {
                    $sql = "update pair_last_change set last = ".$row[1].", um = ".$row[2].", cinco = ".$row[3].", dez = ".$row[4].", trinta = ".$row[5].", hora = ".$row[6]." where pair= '".$row[0]."'"; 
                    
                    if($conn->query($sql)) {
                          
                    }else{
                      throw new exception ($conn->error);
                    }
                     
                   }                        
                   foreach ($pair_volume_change as $row) {
                    $sql = "update pair_volume_change set volume = ".$row[1].", um = ".$row[2].", cinco = ".$row[3].", dez = ".$row[4].", trinta = ".$row[5].", hora = ".$row[6]." where pair= '".$row[0]."'"; 
                    
                    if($conn->query($sql)) {
                          
                    }else{
                      throw new exception ($conn->error);
                    }
                 }                        
                  
          
          
              return array($pair_last_change, $pair_volume_change);  */                                  
            }                                  
                                    
        function trade_history($conn) {
            $date = date('Y-m-d H:i:s');
            echo "trade_history  ".$date.PHP_EOL;
            $trade_history = array();
            $pair_new = array();
          
            $sql = "Select pair, last, um, cinco from pair_last_change order by um desc limit 15";
            if (!($result = $conn->query($sql))){
              throw new exception ($conn->error);
            }

            
              while ($pair = $result->fetch_assoc()) {
              $time = time() - (60*3);
              $timen = time();
              //appendlog($pair[0], "poloniex.log");  
              $http = "https://poloniex.com/public?command=returnTradeHistory&currencyPair=".$pair['pair']."&start=".$time."&end=".$timen;    
              $header = [];
            
              $response= request($http, $header);
              sleep(1);  
              //appendlog($response, "poloniex.log");                     
              
              $json = json_decode($response);
              $totalBTC=0;
                
              foreach ($json as $trade) {
                if ($trade->type == "buy"){
                    $totalBTC = $totalBTC+$trade->total;
                }
              }
                
              array_push($pair_new, [$pair['pair']=>$totalBTC]);
              //array_push($pair_new, $pair);  
            }
          
            //unset($value);
          
                   while ($row = $result->fetch_assoc()) {
                    $sql = "update pair_last_change set totalBTC = ".$pair_new[$row['pair']]." where pair= '".$row['pair']."'"; 
                    
                    if($conn->query($sql)) {
                          
                    }else{
                      throw new exception ($conn->error);
                    }
                     
                   } 
          
            return $pair_new;
        }

              

              $servername = "localhost";
              $username = "root";
              $pass = "";
          
              $conn = new mysqli($servername, $username, $pass);
          
              if ($conn->connect_error){
                die("FAIL!!");
              }
          
              $sql = "use poloniex";
          
              $conn->query($sql);

              $timers = 0;

        //trouxa($conn);

       oldfeed($conn);

       while(true) {

        newfeed($conn);
        $timers = $timers +1;
          
        if ($timers == 99) {
          
          clrdatabase($conn);
          $timers = 0;
          
        }  
         
          
        }  
        // */ 

        $conn->close();       

        //echo $response;
?>