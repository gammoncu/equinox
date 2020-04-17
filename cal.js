var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");
var radius = canvas.height / 2;
ctx.translate(radius, radius);
radius = radius * 0.90
setInterval(drawClock, 1000);

function strmin(et){
  var rad = (((parseInt(et.slice(0,2))%12)*60+parseInt(et.slice(3,5)))/(12*60))*2*Math.PI;
  return rad-(0.5*Math.PI); 
}

//length of radious
function radcal(et){
  if((parseInt(et.slice(0,2))/12)<1){
    return 0.88;
    }else{
      return 1;
      } 
}


function drawClock() {
  drawFace(ctx, radius);
  //drawNumbers(ctx, radius);
  drawTime(ctx, radius);
  drawCal4(ctx, radius);
  drawCen(ctx, radius);
}

 
function getRandomColor() {
  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
} 

//get colors defined by time
function getColor(x) {
  var f = parseInt(x.slice(0,2));
  var n = parseInt(x.slice(3,5));
  var color = "rgb( 2" + (f*2) + ", 2" + (f*2) + ", 2" + (n-5) + ")";
  return color;
} 

  
function drawCal4(ctx, radius) {
  var st = document.getElementsByClassName("cst");
  var et = document.getElementsByClassName("cet");
  var s ="";
  var e ="";
  for (var i = 0; i < st.length; i++) {
    ctx.lineWidth = radius*0.11;
    ctx.lineCap = "circle";//"square";
    ctx.strokeStyle = getColor(st[i].innerHTML);
    ctx.beginPath();
    ctx.arc(0, 0, radius*radcal(st[i].innerHTML), strmin(st[i].innerHTML) , strmin(et[i].innerHTML));
    ctx.stroke();
  }
}


function drawFace(ctx, radius) {
  var grad;
  ctx.beginPath();
  ctx.arc(0, 0, radius, 0, 2*Math.PI);
  ctx.fillStyle = 'black';//was white
  ctx.fill();
  ctx.lineWidth = radius*0.2;
  ctx.strokeStyle = "black";
  ctx.stroke();
  //dashes
  for(let i = 0; i < 60; i++){
    ctx.strokeStyle= "white";
    ctx.lineWidth = radius*0.01;
    let a = Math.PI * 2 * (i/60) - Math.PI * 0.5;
    ctx.beginPath();
    ctx.moveTo(radius*0.85*Math.cos(a), radius*0.85*Math.sin(a));
    ctx.lineTo(radius*0.9*Math.cos(a), radius*0.9*Math.sin(a));
    ctx.stroke();
  }
  //logo
  var image = document.createElement('IMG');
  image.src = 'eq.PNG';
  ctx.font = radius*0.15 + "px Tahoma";
  ctx.textBaseline="middle";
  ctx.textAlign="center";
  ctx.fillStyle="white";
  //ctx.fillText("equinox", 0, -90); 
  var r = (radius/100)
  ctx.drawImage(image, -35*r, -50*r, 80*r, 20*r); 
}
  
function drawCen(ctx, radius) {
  ctx.beginPath();
  ctx.arc(0, 0, radius*0.2, 0, 2*Math.PI);
  ctx.fillStyle = 'black';
  ctx.fill();
  ctx.lineWidth = radius*0.01;
  ctx.strokeStyle = "yellow";
  ctx.stroke();
}


function drawNumbers(ctx, radius) {
  var ang;
  var num;
  ctx.font = radius*0.15 + "px Courier New";
  ctx.textBaseline="middle";
  ctx.textAlign="center";
  ctx.fillStyle="yellow";
  for(num = 1; num < 13; num++){
    ang = num * Math.PI / 6;
    ctx.rotate(ang);
    ctx.translate(0, -radius*0.85);
    ctx.rotate(-ang);
    ctx.fillText(num.toString(), 0, 0);
    ctx.rotate(ang);
    ctx.translate(0, radius*0.85);
    ctx.rotate(-ang);
  }
}

function drawTime(ctx, radius){
    var now = new Date();
    var hour = now.getHours();
    var minute = now.getMinutes();
    var second = now.getSeconds();
    //hour
    hour=hour%12;
    hour=(hour*Math.PI/6)+
    (minute*Math.PI/(6*60))+
    (second*Math.PI/(360*60));
    drawHand(ctx, hour, radius*0.6, radius*0.03);
    //minute
    minute=(minute*Math.PI/30)+(second*Math.PI/(30*60));
    drawHand(ctx, minute, radius*0.8, radius*0.02);
    // second
    second=(second*Math.PI/30);
    drawHand(ctx, second, radius, radius*0.01);
    
}

function drawHand(ctx, pos, length, width) {
    ctx.beginPath();
    ctx.lineWidth = width;
    ctx.strokeStyle = "yellow";
    ctx.lineCap = "triangle";
    ctx.moveTo(0,0);
    ctx.rotate(pos);
    ctx.lineTo(length*0.5, -length*0.5);
    ctx.stroke();
    ctx.rotate(-pos);
}


