function start() {
    
  var campos = document.getElementsByClassName("campo");
  // var campos = $(".campos");
  var linha = 0;
  var campo = 0;
  var linhaletra = "A"
  var i = 0;
  var countplayer = 1;
  var games = new Array(3);
  
  for (var p = 0; p < 3; p++) {
  games[p] = new Array(3);
}
  
  var situacao = function() {
     //player X  
     if (totalarray(games[0]) ==3 || totalarray(games[1]) ==3 || totalarray(games[2]) ==3 || totalarrayV(games, 3) == 1 || totalarrayxv(games, 3) ==1 ) {
       alert("Player O venceu");
       block();
      }else if(totalarray(games[0]) ==-3 || totalarray(games[1]) ==-3 || totalarray(games[2]) ==-3 || totalarrayV(games, -3) == 1 || totalarrayxv(games, -3) ==1 ) {
       alert("Player X venceu");
       block();
      }else if (empate(games)===true){
        
      }
     
    
  }
  
    var acao = function() {  
    var id = this.getAttribute("id");
    var idl = id.charAt(0);
    var idc = id.charAt(1);
    
    if (games[idl][idc] !== 0) {
      return false
    }
    
    if (countplayer%2===0) {
        games[idl][idc]+=1;
        document.getElementById(id).innerHTML="O";
    } else {
        games[idl][idc]-=1;
        document.getElementById(id).innerHTML="X";
    }
      
      console.log("id campo"+id);
      console.log("game campo"+games[idl][idc]);
      countplayer++;
      situacao();
};
  
  
  
   for (i; i<campos.length; i++) {

     console.log("1");
     
     games[linha][campo] = 0; 
     campos[i].id = linha.toString()+campo.toString();
     campos[i].addEventListener('click', acao, false);     
     campo++;
     
      if (i+1==3 || i+1==6) {
        campo = 0;
        linha++;        
     }
     
   }
    
}

function totalarray(array) {
  var total=0;
  for (var i = 0; i<array.length; i++) {
    total+=array[i];    
  }
  console.log("total array linha :"+i+": "+total);
  return total;
}

function totalarrayV (array, num) {
  var total=0;
  for (var i =0; i<array.length; i++) {
    for (var o=0; o<array[i].length; o++){
      
       total+=array[o][i]; 
      
    }
    console.log("total coluna "+i+": "+total);
    if (total == num) {
       return 1
    }else{
      total =0;
    }   
  }
  return 0
}

function totalarrayxv(array, num) {
  var total= 0
  for (var i = 0; i<array.length; i++) {
      for (var o = 0; o<array.length; o++){
        if (i == o) {
          total+=array[i][o];
        }        
      }
      if (total == num) {
        return 1;
      }    
  }
  if (array[0][2]+array[1][1]+array[2][0] == num){
    return 1;
  }
}

function block() {
  var overlay = document.createElement("div");
  overlay.style.width = "176px";
  overlay.style.height = "176px";
  overlay.style.position = "absolute";
  overlay.style.top = "0px";
  $(overlay).click(function(){alert("jogo finalizado")});
  document.getElementById("corpo").appendChild(overlay);
  location.reload();
}

function empate(array) {
  var total = 0
  for (var i = 0; i<array.length; i++) {
    for (var index = 0; index < array[i].length; index++) {
      if (array[i][index] !==0){
        total++
      }      
    }
  }
  if (total==9){
    alert("Jogo empatado, nenhum vencedor!");
    block();
  }
}



//INDEX_BIT.HTML

function load() { 
  order("last_valor", "um_c");
  order("pair_last_change", "time");
  order("return_ticker", "last");    
  
    setInterval(function() {      
      order("last_valor", "um_c");
      order("pair_last_change", "time");
      order("return_ticker", "last");
    }, (30*1000));      

}

function preload(){
  getphp("last_valor", "cinco");
  getphp("volume_valor", "cinco");
  getphp("returnticker", "cinco");  
}

function getphp($table, $order) {
  if(window.XMLHttpRequest) {
    xmlhttp = new XMLHttpRequest();
  }
  xmlhttp.onreadystatechange = function () {
      $("#"+$table+"> tbody").html("");
      $("#"+$table+"> tbody").append(this.responseText);
    
  }
  xmlhttp.open("GET","getsql.php?db="+$table+"&order="+$order);
  xmlhttp.send();
  
}

function order($table, $order) {
    getphp($table, $order);
}




















