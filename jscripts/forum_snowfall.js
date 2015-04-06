// CREDITS:	checked in IE6,FF1.06,Opera7.54,Opera9.01
// Snowmaker Copyright (c) 2003 Peter Gehrig. All rights reserved.
// Modified (c) 2005, Igor Svetlov, Nickname: 12345c
// Location: http://javascript.aho.ru/example/xmp002/falling-snow.htm
// CoLocation: http://forum.vingrad.ru/topic-76142
// Permission given to use the script provided that this notice remains as is.

d=document;
g=function(X){return document.getElementById(X);}
var unitsNum=30;
var snowColor=["#c4bbcc","#ccddFF","#ccd6DD"]
var unitFont=["Arial Black","Arial Narrow","Times","Comic Sans MS"]
var unitText=["<img src='http://www.morgenshtern.com/img/winter/snow.gif' alt='Morgenshtern' title='' />","<img src='http://www.morgenshtern.com/img/winter/snow2.gif' alt='Morgenshtern' title='' />"];
var sinkSpeed=0.4;
var snowSizeMax=30;
var snowSizeMin=24;
var snowingLeft=0.0;
var snowingWidth=1.0;
var snowingHeight=1.0;
var opac=0.65;
var stepTime=40;
var snowOnScreen=1;
var stepIncrease=1.006;
var marginBottom,marginRight,scrlTop=0;
var snow=[], x_mv=[], crds=[], lftRght=[];

var isOpera=self.opera  
var ie5=d.all&&d.getElementById&&!isOpera
var ns6=d.getElementById&&!d.all
var opera9=isOpera&&(document.body.style.opacity=='')
var browserOk=ie5||ns6||isOpera
var ww=0,timer=0;
d.write('<div id="snowZone" style="position:absolute"></div>');
var unitsNumOld=0;
function createSnow(){
for(i=0;i<Math.max(unitsNum,unitsNumOld);i++){if(i<unitsNum){
	var o=d.createElement('SPAN');
	if(g('s'+i)){snowZone.replaceChild(o,g('s'+i));}
	else{snowZone.appendChild(o);}	
	o.setAttribute('id','s'+i);
	o.style.position='absolute';
	o.style.top=0-snowSizeMax;
	if(ns6)o.style.MozOpacity=opac;
	if(opera9)o.style.opacity=opac;
	if(ie5){o.style.filter='alpha';o.filters[0].opacity=opac*100;}
	snowZone.childNodes[i].innerHTML=unitText[randomMaker(unitText.length)];
}else{snowZone.removeChild(g('s'+i));}
}unitsNumOld=unitsNum;}
function randomMaker(range){return Math.floor(range*Math.random())}

function botRight(){marginBottom=((ns6?innerHeight:d.body.clientHeight)-1.5*snowSizeMax)*snowingHeight;
	marginRight=ns6?innerWidth:d.body.clientWidth;}
function checkPgDn(){if(snowOnScreen)g('snowZone').style.top=ns6?pageYOffset:document.body.scrollTop;
  scrlTop=ns6?pageYOffset:document.body.scrollTop;}
function newPosSnow(y,i){var o;
	snow[i].posY=y+(snowOnScreen?0:(ns6?pageYOffset:d.body.scrollTop));
	snow[i].size=(randomMaker(snowSizeRange)+snowSizeMin)/6;
		if(snow[i].hasChildNodes()&&(o=snow[i].childNodes[0]).tagName=='IMG')o.width=o.height=snow[i].size/1.6;
		else snow[i].style.fontSize=snow[i].size/6;
	snow[i].posX=randomMaker(marginRight*snowingWidth-2*snow[i].size)+marginRight*snowingLeft;
	if(ie5)snow[i].filters[0].opacity=opac*100;if(ns6||opera9)snow[i].style.opacity=opac;
	setTimeout("snow["+i+"].style.visibility='visible';",250);
}
function moveSnow() {
	for (i=0;i<unitsNum;i++) {
		snow[i].style.top=snow[i].posY+=snow[i].sink+lftRght[i]*Math.sin(crds[i])/3;
		crds[i] += x_mv[i];
		var b=snow[i].posX+lftRght[i]*Math.sin(crds[i]);
		var a=marginBottom+(snowOnScreen?0:scrlTop)-1.5*snow[i].size-snow[i].posY;
		snow[i].size*=stepIncrease;
		if(a<=0 || b>marginRight-snow[i].size-25)
			{snow[i].style.visibility='hidden';newPosSnow(randomMaker((marginBottom-3*snow[i].size)/2),i);continue;}	
		snow[i].style.left=b;
		if(snow[i].hasChildNodes()&&(o=snow[i].childNodes[0]).tagName=='IMG')o.width=o.height=snow[i].size/1.6;
		else snow[i].style.fontSize=snow[i].size;
		if(a<=9.5*snow[i].size*snowingHeight){if(ie5)snow[i].filters[0].opacity=opac*a/(9.5*snow[i].size)*100;
		else if(ns6||opera9)snow[i].style.opacity=opac*a/(9.5*snow[i].size);}
	}
}
onload=function(){if(browserOk)setTimeout(snowRestart,99);}
onscroll=checkPgDn;
if(self.addEventListener)addEventListener('DOMMouseScroll',function(){setTimeout(onscroll,1)},!1);
onresize=botRight;
genBr5x=function(N){var s='';while(N>0){s+=N+'<br><br><br><br><br>';N-=5;}return s;}
function snowRestart(s){ if(s)eval(s);
	createSnow();
	snowSizeRange=snowSizeMax-snowSizeMin;
	clearInterval(ww);clearTimeout(timer);
	checkPgDn();if(ns6)ww=setInterval(checkPgDn,999);
	botRight();
	for (i=0;i<unitsNum;i++) {	crds[i] = 0;                      
    			lftRght[i] = Math.random()*20;         
    			x_mv[i] = 0.03 + Math.random()/10;
		snow[i]=g("s"+i)
		snow[i].style.fontFamily=unitFont[randomMaker(unitFont.length)]
		snow[i].size=randomMaker(snowSizeRange)+snowSizeMin;
		if(snow[i].hasChildNodes()&&(o=snow[i].childNodes[0]).tagName=='IMG')o.width=o.height=snow[i].size/1.6;
		else snow[i].style.fontSize=snow[i].size/6;
		snow[i].style.color=snowColor[randomMaker(snowColor.length)]
		snow[i].sink=sinkSpeed*snow[i].size/5
		newPosSnow(randomMaker(marginBottom-3*snow[i].size),i);
	}if(unitsNum)timer=setInterval(moveSnow,stepTime);else{clearInterval(timer);clearInterval(ww);} 
}