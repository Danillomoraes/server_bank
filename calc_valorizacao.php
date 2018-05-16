<?php

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

              $servername = "localhost";
              $username = "root";
              $pass = "";
          
              $conn = new mysqli($servername, $username, $pass);

              $sql ="use poloniex";

              $conn->query($sql);
            
              //trouxa($conn);


              while (true) {

              
              $date = date('Y-m-d H:i:s');
              echo "calc_valorizacao  ".$date.PHP_EOL;
          
              $sql = "select pair, last from pair_last_change";
              $result = $conn->query($sql);
              
              while ($row = $result->fetch_assoc()) {
                
                $time = time()-((60*60)+10);
                $sql = "select * from returnticker where pair = '".$row['pair']."' and hora >= ".$time."  order by hora desc";
                $res = $conn->query($sql);
                //echo $conn->error.PHP_EOL;
                
                 //um minuto; h=high, l=low;
                
                $last = $res->fetch_row();
                
                $um = $last[2];
                $hum=$last[2];
                $lum=$last[2];
                
                //cinco minnutos;
                
                $cinco = $last[2];
                $hcinco = $last[2];
                $lcinco = $last[2];
                
                //dez minutos;
                
                $dez = $last[2];
                $hdez = $last[2];
                $ldez = $last[2];
                
                //trinta minutos;
                
                $trinta = $last[2];
                $htrinta = $last[2];
                $ltrinta = $last[2];
                
                //hora
                
                $hora = $last[2];
                $hhora = $last[2];
                $lhora = $last[2];      
                  
                $res = $conn->query($sql);
                
               
                while ($linha = $res->fetch_assoc()) {
                  
                
                //  echo print_r($linha).PHP_EOL;
                
                $time = time();  
                $umminuto = time()-60;
                $cincominuto = $umminuto-240;
                $dezminuto = $cincominuto-300;
                $trintaminuto = $dezminuto-1200;
                $horaminuto = $trintaminuto-1800;                                
                
                                    
                  //calculo um minuto
                  
                  if ($linha['hora']>=$umminuto){
                    $um = $linha['last'];
                    if ($linha['last']>$hum){
                      $hum = $linha['last'];
                    }
                    if ($linha['last']<$lum) {
                      $lum = $linha['last'];
                    }
                  }  
                    
                  //calculo cinco minuto
                  
                  if ($linha['hora']>=$cincominuto){
                    $cinco = $linha['last'];
                    if ($linha['last']>$hcinco){
                      $hcinco = $linha['last'];
                    }
                    if ($linha['last']<$lcinco) {
                      $lcinco = $linha['last'];
                    }  
                    
                  }
                  
                  //calculo dez minuto
                  
                  if ($linha['hora']>=$dezminuto){
                    $dez = $linha['last'];
                    if ($linha['last']>$hdez){
                      $hdez = $linha['last'];
                    }
                    if ($linha['last']<$ldez) {
                      $ldez = $linha['last'];
                    }  
                    
                  }
                  
                  //calculo trinta minuto
                  
                  if ($linha['hora']>=$trintaminuto){
                    $trinta = $linha['last'];
                    if ($linha['last']>$htrinta){
                      $htrinta = $linha['last'];
                    }
                    if ($linha['last']<$ltrinta) {
                      $ltrinta = $linha['last'];
                    }  
                    
                  }
                  
                  //calculo hora minuto
                  
                  if ($linha['hora']>=$horaminuto){
                    $hora = $linha['last'];
                    if ($linha['last']>$hhora){
                      $hhora = $linha['last'];
                    }
                    if ($linha['last']<$lhora) {
                      $lhora = $linha['last'];
                    }  
                    
                   }                                     
                  
                 }
                
                
                $sql = "update pair_last_change set
                last= ".$last[2].", 
                um= ".$um.", 
                hum= ".$hum.",
                lum= ".$lum.", 
                cinco= ".$cinco.", 
                hcinco= ".$hcinco.", 
                lcinco= ".$lcinco.", 
                dez= ".$dez.", 
                hdez= ".$hdez.", 
                ldez= ".$ldez.", 
                trinta= ".$trinta.", 
                htrinta= ".$htrinta.", 
                ltrinta= ".$ltrinta.", 
                hora= ".$hora.", 
                hhora= ".$hhora.", 
                lhora= ".$lhora.",
                time= ".$time."
                
                where pair ='".$row['pair']."'";
                
                if(!$conn->query($sql)) {
              
                }
                    echo $conn->error;
                  
                }

        //      $arr = get_defined_vars();
        //      print_r($arr)
                
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
            

?>