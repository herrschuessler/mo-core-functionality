(async()=>{const e=MoCoreExternalSvgSprite.iconSetBaseUrl.replace(/[.*+?^${}()|\/[\]\\]/g,"\\$&"),t=Array.from(document.querySelectorAll("svg use")).map((t=>{const r=t.getAttribute("xlink:href")||t.getAttribute("href");return r&&r.match(new RegExp(`^${e}`))?{elem:t,url:r}:null})).filter((e=>!!e)).map((t=>{const r=t.url.match(new RegExp(`^${e}((?:(?!.svg).)+).svg[^#]*#(.+)`));return{setId:r[1],iconId:r[2],...t}})),r={};t.forEach((e=>{let{url:t,setId:n}=e;r[n]||(r[n]=t.split("#")[0])}));const n=await Promise.all(Object.values(r).map((e=>fetch(e).then((e=>e.text()))))).then((e=>e.join(""))),l=document.createElement("div");l.style.display="none",l.innerHTML=n,document.body.insertBefore(l,document.body.childNodes[0]),t.forEach((e=>{let{elem:t,iconId:r}=e;t.removeAttribute("xlink:href"),t.removeAttribute("href"),t.setAttribute("href",`#${r}`)}))})();