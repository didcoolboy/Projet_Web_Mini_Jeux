const TICK=50;
let state={},intervalId=null,stars=[];

function getZP(s){ return s.minVal+15; } // pour les décors
function gaugeToAlt(gauge){ return gauge * 80; } // altitude affichée = jauge * 80

function mkStars(){
  stars=[];
  for(let i=0;i<180;i++) stars.push({x:Math.random(),y:Math.random(),r:Math.random()*1.8+0.3,tw:Math.random()*Math.PI*2});
}

function initState(){
  state={running:false,score:0,timeMs:0,
    gauge:0,      // position jauge = altitude
    maxVal:15,    // plafond (monte avec MAX)
    minVal:-15,   // plancher irréversible (monte avec MIN, drive les décors)
    pressing:{inflate:false,max:false,min:false,repair:false},
    overheat:{max:0,min:0},broken:{max:false,min:false},repairProgress:0};
}

const BASE_DROP=0.10;
function inflateBoost(s){const gap=s.maxVal-s.minVal;return 0.55*Math.max(0.04,Math.pow(30/Math.max(gap,5),1.4));}
function boostPct(s){return Math.min(100,(inflateBoost(s)/0.55)*100);}
function fade(v,s,e){return Math.min(1,Math.max(0,(v-s)/Math.max(1,e-s)));}

function getSkyColors(zp){
  const z=[
    {a:0,s:[135,206,235],g:[34,139,34]},
    {a:8,s:[110,175,225],g:[80,160,80]},
    {a:20,s:[70,120,200],g:[160,200,220]},
    {a:40,s:[30,60,160],g:[80,100,150]},
    {a:70,s:[8,18,70],g:[15,20,60]},
    {a:110,s:[2,3,15],g:[2,3,15]},
    {a:180,s:[0,0,3],g:[0,0,3]},
  ];
  let a=z[0],b=z[z.length-1];
  for(let i=0;i<z.length-1;i++){if(zp>=z[i].a&&zp<z[i+1].a){a=z[i];b=z[i+1];break;}}
  const t=Math.min(1,(zp-a.a)/Math.max(1,b.a-a.a));
  const l=(x,y)=>Math.round(x+(y-x)*t);
  return{s:[l(a.s[0],b.s[0]),l(a.s[1],b.s[1]),l(a.s[2],b.s[2])],g:[l(a.g[0],b.g[0]),l(a.g[1],b.g[1]),l(a.g[2],b.g[2])]};
}

let bgCanvas,bgCtx,ovCanvas,ovCtx;

function drawUFO(W,H,alpha){
  const now=Date.now()/1000,ux=W*0.3+Math.sin(now*0.7)*W*0.12,uy=H*0.35+Math.sin(now*1.1)*H*0.06;
  bgCtx.save();bgCtx.globalAlpha=alpha;
  const bg=bgCtx.createLinearGradient(ux,uy+10,ux,uy+55);
  bg.addColorStop(0,'rgba(180,255,180,0.35)');bg.addColorStop(1,'rgba(180,255,180,0)');
  bgCtx.beginPath();bgCtx.moveTo(ux-18,uy+10);bgCtx.lineTo(ux+18,uy+10);bgCtx.lineTo(ux+32,uy+55);bgCtx.lineTo(ux-32,uy+55);bgCtx.closePath();bgCtx.fillStyle=bg;bgCtx.fill();
  bgCtx.beginPath();bgCtx.ellipse(ux,uy+4,30,10,0,0,Math.PI*2);bgCtx.fillStyle='rgba(160,160,180,1)';bgCtx.fill();
  bgCtx.strokeStyle='rgba(200,200,220,0.8)';bgCtx.lineWidth=1.5;bgCtx.stroke();
  bgCtx.beginPath();bgCtx.ellipse(ux,uy-2,14,10,0,Math.PI,Math.PI*2);bgCtx.fillStyle='rgba(120,220,255,0.7)';bgCtx.fill();
  [-16,-8,0,8,16].forEach((ox,i)=>{bgCtx.beginPath();bgCtx.arc(ux+ox,uy+8,2.5,0,Math.PI*2);bgCtx.fillStyle=Math.sin(Date.now()/250+i)>0?`hsl(${i*60},100%,70%)`:'rgba(60,60,60,0.8)';bgCtx.fill();});
  bgCtx.restore();
}

function drawGalaxy(W,H,alpha){
  const now=Date.now()/1000,cx=W*0.65,cy=H*0.22;
  bgCtx.save();bgCtx.globalAlpha=alpha;
  const glow=bgCtx.createRadialGradient(cx,cy,0,cx,cy,55);
  glow.addColorStop(0,'rgba(200,160,255,0.5)');glow.addColorStop(0.4,'rgba(140,100,220,0.2)');glow.addColorStop(1,'rgba(0,0,0,0)');
  bgCtx.fillStyle=glow;bgCtx.beginPath();bgCtx.arc(cx,cy,55,0,Math.PI*2);bgCtx.fill();
  for(let arm=0;arm<3;arm++){
    const base=(arm/3)*Math.PI*2+now*0.08;
    for(let j=0;j<18;j++){const t=j/18,r=t*48,a=base+t*Math.PI*1.6;bgCtx.beginPath();bgCtx.arc(cx+Math.cos(a)*r,cy+Math.sin(a)*r*0.45,(1-t)*3.5+0.5,0,Math.PI*2);bgCtx.fillStyle=`rgba(210,180,255,${(1-t)*0.8})`;bgCtx.fill();}
  }
  bgCtx.beginPath();bgCtx.arc(cx,cy,7,0,Math.PI*2);bgCtx.fillStyle='rgba(255,240,200,0.95)';bgCtx.fill();
  bgCtx.restore();
}

function drawBg(){
  const W=bgCanvas.width,H=bgCanvas.height;
  const zp=getZP(state); // décors basés sur MIN (irréversible)
  const col=getSkyColors(zp);
  const grad=bgCtx.createLinearGradient(0,0,0,H);
  grad.addColorStop(0,`rgb(${col.s})`);grad.addColorStop(1,`rgb(${col.g})`);
  bgCtx.fillStyle=grad;bgCtx.fillRect(0,0,W,H);

  const starA=fade(zp,35,60);
  if(starA>0){const now=Date.now()/1000;stars.forEach(st=>{bgCtx.beginPath();bgCtx.arc(st.x*W,st.y*H,st.r,0,Math.PI*2);bgCtx.fillStyle=`rgba(255,255,255,${starA*(0.6+0.4*Math.sin(now*1.6+st.tw))})`;bgCtx.fill();});}

  if(zp<8){
    const gA=Math.max(0,1-zp/6),groundY=H*(0.78+0.18*gA);
    bgCtx.fillStyle=`rgba(30,120,30,${gA*0.95})`;bgCtx.fillRect(0,groundY,W,H-groundY);
    if(zp<4){
      const hA=Math.max(0,1-zp/3),hx=W*0.5,hy=groundY-55;
      bgCtx.fillStyle=`rgba(180,120,60,${hA})`;bgCtx.fillRect(hx-22,hy,44,55);
      bgCtx.fillStyle=`rgba(190,55,55,${hA})`;bgCtx.beginPath();bgCtx.moveTo(hx-30,hy);bgCtx.lineTo(hx,hy-32);bgCtx.lineTo(hx+30,hy);bgCtx.closePath();bgCtx.fill();
      bgCtx.fillStyle=`rgba(20,100,20,${hA*0.9})`;
      [0.15,0.27,0.66,0.79,0.9].forEach(tx=>{const ty=groundY-38;bgCtx.fillRect(tx*W-3,ty,6,38);bgCtx.beginPath();bgCtx.arc(tx*W,ty,15,0,Math.PI*2);bgCtx.fill();});
    }
  }
  if(zp>4&&zp<18){
    const cA=Math.min(1,Math.max(0,1-Math.abs(zp-11)/7))*0.48;
    bgCtx.fillStyle=`rgba(255,255,255,${cA})`;
    [[0.18,0.38],[0.52,0.25],[0.73,0.52],[0.35,0.62]].forEach(([cx,cy])=>{bgCtx.beginPath();bgCtx.ellipse(cx*W,cy*H,68,25,0,0,Math.PI*2);bgCtx.fill();});
  }
  if(zp>38){const pa=fade(zp,38,58);bgCtx.beginPath();bgCtx.arc(W*0.74,H*0.2,42,0,Math.PI*2);bgCtx.fillStyle=`rgba(180,90,45,${pa})`;bgCtx.fill();bgCtx.beginPath();bgCtx.arc(W*0.74,H*0.2,42,-0.5,0.5);bgCtx.strokeStyle=`rgba(220,150,80,${pa*0.5})`;bgCtx.lineWidth=9;bgCtx.stroke();}
  if(zp>65){const pa=fade(zp,65,85);bgCtx.beginPath();bgCtx.arc(W*0.2,H*0.72,22,0,Math.PI*2);bgCtx.fillStyle=`rgba(60,100,200,${pa})`;bgCtx.fill();bgCtx.beginPath();bgCtx.ellipse(W*0.2,H*0.72,36,8,-0.3,0,Math.PI*2);bgCtx.strokeStyle=`rgba(120,160,240,${pa*0.6})`;bgCtx.lineWidth=4;bgCtx.stroke();}
  if(zp>90) drawUFO(W,H,fade(zp,90,110));
  if(zp>120) drawGalaxy(W,H,fade(zp,120,145));
  if(zp>165){
    const pa=fade(zp,165,190),nx=W*0.5,ny=H*0.6;
    const neb=bgCtx.createRadialGradient(nx,ny,0,nx,ny,80);
    neb.addColorStop(0,`rgba(255,100,150,${pa*0.6})`);neb.addColorStop(0.5,`rgba(100,50,200,${pa*0.3})`);neb.addColorStop(1,'rgba(0,0,0,0)');
    bgCtx.fillStyle=neb;bgCtx.beginPath();bgCtx.arc(nx,ny,80,0,Math.PI*2);bgCtx.fill();
  }
}

function drawOverlay(){
  const W=ovCanvas.width,H=ovCanvas.height;
  ovCtx.clearRect(0,0,W,H);
  const s=state,VIEW=40,topVal=s.maxVal+VIEW*0.3,botVal=topVal-VIEW*2;
  function vToPx(v){return H*(1-(v-botVal)/(topVal-botVal));}
  const gX=W*0.08,gW=W*0.06,gH=H*0.88,gY=H*0.06;
  const maxPx=Math.max(gY,Math.min(gY+gH,vToPx(s.maxVal)));
  const minPx=Math.max(gY,Math.min(gY+gH,vToPx(s.minVal)));
  const gaugePx=Math.max(gY,Math.min(gY+gH,vToPx(s.gauge)));
  ovCtx.fillStyle='rgba(0,0,0,0.32)';ovCtx.beginPath();ovCtx.roundRect(gX-5,gY-5,gW+10,gH+10,9);ovCtx.fill();
  ovCtx.fillStyle='rgba(29,158,117,0.18)';ovCtx.fillRect(gX,maxPx,gW,minPx-maxPx);
  ovCtx.fillStyle='rgba(255,255,255,0.05)';ovCtx.beginPath();ovCtx.roundRect(gX,gY,gW,gH,6);ovCtx.fill();
  const ratio=(s.gauge-s.minVal)/Math.max(1,s.maxVal-s.minVal);
  ovCtx.fillStyle=ratio>0.82?'#E24B4A':ratio<0.18?'#378ADD':'#1D9E75';
  if(gaugePx<gY+gH){ovCtx.beginPath();ovCtx.roundRect(gX,gaugePx,gW,(gY+gH)-gaugePx,4);ovCtx.fill();}
  ovCtx.setLineDash([5,3]);ovCtx.lineWidth=2;
  ovCtx.strokeStyle='#E24B4A';ovCtx.beginPath();ovCtx.moveTo(gX-8,maxPx);ovCtx.lineTo(gX+gW+8,maxPx);ovCtx.stroke();
  ovCtx.strokeStyle='#378ADD';ovCtx.beginPath();ovCtx.moveTo(gX-8,minPx);ovCtx.lineTo(gX+gW+8,minPx);ovCtx.stroke();
  ovCtx.setLineDash([]);
  ovCtx.strokeStyle='rgba(255,255,255,0.4)';ovCtx.lineWidth=1.5;ovCtx.beginPath();ovCtx.roundRect(gX,gY,gW,gH,6);ovCtx.stroke();
  ovCtx.font='bold 11px sans-serif';
  ovCtx.fillStyle='#E24B4A';ovCtx.fillText('MAX',gX+gW+10,maxPx+4);
  ovCtx.fillStyle='#378ADD';ovCtx.fillText('MIN',gX+gW+10,minPx+4);
  const infoX=gX+gW+52,gap=s.maxVal-s.minVal,boost=inflateBoost(s);
  ovCtx.font='11px sans-serif';
  ovCtx.fillStyle='rgba(255,255,255,0.8)';ovCtx.fillText('écart: '+Math.round(gap),infoX,gY+18);
  ovCtx.fillStyle=boost>0.4?'#1D9E75':boost>0.15?'#BA7517':'#E24B4A';
  ovCtx.fillText('boost ×'+boost.toFixed(2),infoX,gY+36);
}

function animLoop(){drawBg();drawOverlay();requestAnimationFrame(animLoop);}

function fmtAlt(v){
  const abs=Math.abs(v);
  if(abs<1000) return Math.round(v)+'m';
  if(abs<1000000) return (v/1000).toFixed(1)+'km';
  return (v/1000000).toFixed(2)+'Mm';
}

function updateUI(){
  const s=state;
  document.getElementById('score-val').textContent=s.score;
  document.getElementById('time-val').textContent=Math.floor(s.timeMs/1000)+'s';
  document.getElementById('min-val').textContent=Math.round(s.minVal);
  document.getElementById('max-val').textContent=Math.round(s.maxVal);
  // altitude = position de la jauge * 80
  document.getElementById('alt-val').textContent=fmtAlt(gaugeToAlt(s.gauge));
  document.getElementById('oh-max').style.width=Math.min(100,s.overheat.max)+'%';
  document.getElementById('oh-min').style.width=Math.min(100,s.overheat.min)+'%';
  document.getElementById('repair-bar').style.width=Math.min(100,s.repairProgress)+'%';
  document.getElementById('speed-fill').style.width=boostPct(s)+'%';
  ['max','min'].forEach(k=>{
    const btn=document.getElementById('btn-'+k);
    btn.classList.toggle('broken',s.broken[k]);
    btn.classList.toggle('pressing',s.pressing[k]&&!s.broken[k]);
    const label=k==='max'?(s.broken.max?'MAX (cassé)':'Monter MAX'):(s.broken.min?'MIN (cassé)':'Monter MIN');
    btn.innerHTML=label+'<span class="btn-key">'+(k==='max'?'[ ← ]':'[ → ]')+'</span>';
  });
  document.getElementById('btn-inflate').classList.toggle('pressing',s.pressing.inflate);
}

function gameLoop(){
  const s=state;if(!s.running)return;
  s.timeMs+=TICK;
  const maxSnap=s.maxVal,minSnap=s.minVal;
  s.gauge-=BASE_DROP;
  s.score=Math.round(s.score-BASE_DROP*10);
  if(s.pressing.inflate){
    const boost=inflateBoost(s),before=s.gauge;
    s.gauge+=boost;
    const gained=Math.max(0,s.gauge-before);
    if(gained>0) s.score=Math.round(s.score+gained*10);
  }
  if(s.gauge<=minSnap){gameOver('Chute sous le MIN !');return;}
  if(s.gauge>=maxSnap){gameOver('Dépassement du MAX !');return;}
  const expF=Math.max(0.05,1-Math.pow(Math.max(0,(s.maxVal-s.minVal)-30)/120,0.55));
  const ohRate=0.5+Math.max(0,(s.maxVal-s.minVal-30)/60)*0.8;
  if(s.pressing.max&&!s.broken.max){s.maxVal+=0.4*expF;s.overheat.max=Math.min(100,s.overheat.max+ohRate);if(s.overheat.max>=100)s.broken.max=true;}
  else{s.overheat.max=Math.max(0,s.overheat.max-0.35);}
  if(s.pressing.min&&!s.broken.min){
    const d=0.4*expF;s.minVal+=d;s.gauge+=d;
    s.overheat.min=Math.min(100,s.overheat.min+ohRate);if(s.overheat.min>=100)s.broken.min=true;
  } else{s.overheat.min=Math.max(0,s.overheat.min-0.35);}
  const anyBroken=s.broken.max||s.broken.min,bothBroken=s.broken.max&&s.broken.min;
  if(s.pressing.repair&&anyBroken){
    s.repairProgress=Math.min(100,s.repairProgress+(bothBroken?0.8:1.6));
    if(s.repairProgress>=100){s.broken.max=false;s.broken.min=false;s.overheat.max=45;s.overheat.min=45;s.repairProgress=0;}
  }
  updateUI();
}

function gameOver(reason){
  state.running=false;clearInterval(intervalId);
  document.getElementById('ot').textContent='Game Over';
  document.getElementById('om').textContent=reason+'\n\nScore : '+state.score+'\nAltitude : '+fmtAlt(gaugeToAlt(state.gauge))+'\nTemps : '+Math.floor(state.timeMs/1000)+'s';
  document.getElementById('overlay-btn').textContent='Rejouer';
  document.getElementById('overlay').style.display='flex';
}

function startGame(){
  initState();state.running=true;
  document.getElementById('overlay').style.display='none';
  clearInterval(intervalId);intervalId=setInterval(gameLoop,TICK);updateUI();
}

function setupCanvas(){
  bgCanvas=document.getElementById('bg-canvas');ovCanvas=document.getElementById('ov-canvas');
  bgCtx=bgCanvas.getContext('2d');ovCtx=ovCanvas.getContext('2d');
  const w=document.getElementById('gauge-wrap');
  bgCanvas.width=ovCanvas.width=w.clientWidth;bgCanvas.height=ovCanvas.height=w.clientHeight;
}

document.getElementById('overlay-btn').addEventListener('click',startGame);
function bindHold(id,key){
  const el=document.getElementById(id);
  el.addEventListener('mousedown',()=>{if(state.running)state.pressing[key]=true;});
  el.addEventListener('mouseup',()=>{state.pressing[key]=false;});
  el.addEventListener('mouseleave',()=>{state.pressing[key]=false;});
  el.addEventListener('touchstart',e=>{e.preventDefault();if(state.running)state.pressing[key]=true;});
  el.addEventListener('touchend',()=>{state.pressing[key]=false;});
}
bindHold('btn-inflate','inflate');bindHold('btn-max','max');bindHold('btn-min','min');bindHold('btn-repair','repair');
const keyMap={'ArrowUp':'inflate','ArrowDown':'repair','ArrowLeft':'max','ArrowRight':'min'};
document.addEventListener('keydown',e=>{if(keyMap[e.key]){e.preventDefault();if(state.running)state.pressing[keyMap[e.key]]=true;}});
document.addEventListener('keyup',e=>{if(keyMap[e.key]){e.preventDefault();state.pressing[keyMap[e.key]]=false;}});

mkStars();
setupCanvas();
initState();
animLoop();