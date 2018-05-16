<?php
$db = $_REQUEST["db"];
$order= $_REQUEST["order"];

              $servername = "localhost";
              $username = "root";
              $pass = "";
          
              $conn = new mysqli($servername, $username, $pass);
          
              if ($conn->connect_error){
                die("FAIL!!");
              }
          
              $sql = "use poloniex";
          
              $conn->query($sql);

              $field="";

              if($db == "last_valor" or $db == "pair_last_change"){
                $field= "last";
              }elseif($db == "volume_valor"){
                $field= "volume";
              }

              if ($db == "last_valor" or $db == "pair_last_change"){

              $i = 1;
              $sql = "select * from  ".$db." order by ".$order." desc limit 15";
              
              if ($result = $conn->query ($sql)){
                
                while($row = $result->fetch_assoc()) {
                  Echo $conn->error;
                  echo "<tr>";
                  echo "<th scope = row>".$i."</th>";
                  $i = $i+1;
                  echo "<td>".$row['pair']."</td>";
                  echo "<td>".$row[$field]."</td>";
                  echo "<td>".$row['um']."(".sprintf('%0.2f', $row['um_c']).")"."</td>";
                  echo "<td>".$row['hum']."(".sprintf('%0.2f', $row['hum_c']).")"."</td>";
                  echo "<td>".$row['lum']."(".sprintf('%0.2f',$row['lum_c']).")"."</td>";
                  echo "<td>".$row['cinco']."(".sprintf('%0.2f', $row['cinco_c']).")"."</td>";
                  echo "<td>".$row['hcinco']."(".sprintf('%0.2f', $row['hcinco_c']).")"."</td>";
                  echo "<td>".$row['lcinco']."(".sprintf('%0.2f', $row['lcinco_c']).")"."</td>";
                  echo "<td>".$row['dez']."(".sprintf('%0.2f', $row['dez_c']).")"."</td>";
                  echo "<td>".$row['hdez']."(".sprintf('%0.2f',$row['hdez_c']).")"."</td>";
                  echo "<td>".$row['ldez']."(".sprintf('%0.2f',$row['ldez_c']).")"."</td>";
                  echo "<td>".$row['trinta']."(".sprintf('%0.2f', $row['trinta_c']).")"."</td>";
                  echo "<td>".$row['htrinta']."(".sprintf('%0.2f', $row['htrinta_c']).")"."</td>";                  
                  echo "<td>".$row['ltrinta']."(".sprintf('%0.2f',$row['ltrinta_c']).")"."</td>";
                  echo "<td>".$row['hora']."(".sprintf('%0.2f',$row['hora_c']).")"."</td>";
                  echo "<td>".$row['hhora']."(".sprintf('%0.2f', $row['hhora_c']).")"."</td>";
                  echo "<td>".$row['lhora']."(".sprintf('%0.2f', $row['lhora_c']).")"."</td>";
                  echo "<td>".gmdate("Y-m-d\H:i:s", $row['hora'])."</td>";
                  echo "</tr>";
                  
                }
                
              }else{
                
                echo $conn->error;
                
              }
                
              }else {
                
                 $i = 1;
                 $sql = "select pair from pair_last_change";
      
                 $other = $conn->query($sql);
                
                 $pair = [];
                
                while($row = $other->fetch_assoc()) {
                  $sql = "Select * from returnticker where pair= '".$row['pair']."' order by ".$order." desc limit 1";
          
                  if(!$result = $conn->query($sql)){
                    echo $conn->error;
                  }                 
                  
                  while ($rows = $result->fetch_assoc()) {
                  array_push($pair, [$rows["pair"], $rows["last"], $rows["chang"], $rows["volume"], $rows["hora"]]);
                  }                
              }
                  
                  foreach ($pair as $row){
                  
                  echo "<tr>";
                  echo "<th scope = row>".$i."</th>";
                  $i = $i+1;
                  echo "<td>".$row[0]."</td>";
                  echo "<td>".$row[1]."</td>";
                  echo "<td>".sprintf("%.2f%%", $row[3])."</td>";
                  echo "<td>".$row[3]."</td>";
                  echo "<td>".gmdate("Y-m-d\H:i:s", $row[4])."</td>";
                  echo "</tr>";
                  
                }
                
        
                
                
              }   

              
              $conn->close();
?>