(self.webpackChunk=self.webpackChunk||[]).push([[409],{9669:(t,e,n)=>{t.exports=n(1609)},5448:(t,e,n)=>{"use strict";var r=n(4867),o=n(6026),i=n(4372),s=n(5327),a=n(4097),u=n(4109),c=n(7985),l=n(5061),f=n(5655),d=n(4442);t.exports=function(t){return new Promise((function(e,n){var p,h=t.data,m=t.headers,v=t.responseType;function g(){t.cancelToken&&t.cancelToken.unsubscribe(p),t.signal&&t.signal.removeEventListener("abort",p)}r.isFormData(h)&&delete m["Content-Type"];var b=new XMLHttpRequest;if(t.auth){var y=t.auth.username||"",_=t.auth.password?unescape(encodeURIComponent(t.auth.password)):"";m.Authorization="Basic "+btoa(y+":"+_)}var x=a(t.baseURL,t.url);function w(){if(b){var r="getAllResponseHeaders"in b?u(b.getAllResponseHeaders()):null,i={data:v&&"text"!==v&&"json"!==v?b.response:b.responseText,status:b.status,statusText:b.statusText,headers:r,config:t,request:b};o((function(t){e(t),g()}),(function(t){n(t),g()}),i),b=null}}if(b.open(t.method.toUpperCase(),s(x,t.params,t.paramsSerializer),!0),b.timeout=t.timeout,"onloadend"in b?b.onloadend=w:b.onreadystatechange=function(){b&&4===b.readyState&&(0!==b.status||b.responseURL&&0===b.responseURL.indexOf("file:"))&&setTimeout(w)},b.onabort=function(){b&&(n(l("Request aborted",t,"ECONNABORTED",b)),b=null)},b.onerror=function(){n(l("Network Error",t,null,b)),b=null},b.ontimeout=function(){var e=t.timeout?"timeout of "+t.timeout+"ms exceeded":"timeout exceeded",r=t.transitional||f.transitional;t.timeoutErrorMessage&&(e=t.timeoutErrorMessage),n(l(e,t,r.clarifyTimeoutError?"ETIMEDOUT":"ECONNABORTED",b)),b=null},r.isStandardBrowserEnv()){var S=(t.withCredentials||c(x))&&t.xsrfCookieName?i.read(t.xsrfCookieName):void 0;S&&(m[t.xsrfHeaderName]=S)}"setRequestHeader"in b&&r.forEach(m,(function(t,e){void 0===h&&"content-type"===e.toLowerCase()?delete m[e]:b.setRequestHeader(e,t)})),r.isUndefined(t.withCredentials)||(b.withCredentials=!!t.withCredentials),v&&"json"!==v&&(b.responseType=t.responseType),"function"==typeof t.onDownloadProgress&&b.addEventListener("progress",t.onDownloadProgress),"function"==typeof t.onUploadProgress&&b.upload&&b.upload.addEventListener("progress",t.onUploadProgress),(t.cancelToken||t.signal)&&(p=function(t){b&&(n(!t||t&&t.type?new d("canceled"):t),b.abort(),b=null)},t.cancelToken&&t.cancelToken.subscribe(p),t.signal&&(t.signal.aborted?p():t.signal.addEventListener("abort",p))),h||(h=null),b.send(h)}))}},1609:(t,e,n)=>{"use strict";var r=n(4867),o=n(1849),i=n(321),s=n(7185);var a=function t(e){var n=new i(e),a=o(i.prototype.request,n);return r.extend(a,i.prototype,n),r.extend(a,n),a.create=function(n){return t(s(e,n))},a}(n(5655));a.Axios=i,a.Cancel=n(4442),a.CancelToken=n(4972),a.isCancel=n(6502),a.VERSION=n(7288).version,a.all=function(t){return Promise.all(t)},a.spread=n(8713),a.isAxiosError=n(6268),t.exports=a,t.exports.default=a},4442:t=>{"use strict";function e(t){this.message=t}e.prototype.toString=function(){return"Cancel"+(this.message?": "+this.message:"")},e.prototype.__CANCEL__=!0,t.exports=e},4972:(t,e,n)=>{"use strict";var r=n(4442);function o(t){if("function"!=typeof t)throw new TypeError("executor must be a function.");var e;this.promise=new Promise((function(t){e=t}));var n=this;this.promise.then((function(t){if(n._listeners){var e,r=n._listeners.length;for(e=0;e<r;e++)n._listeners[e](t);n._listeners=null}})),this.promise.then=function(t){var e,r=new Promise((function(t){n.subscribe(t),e=t})).then(t);return r.cancel=function(){n.unsubscribe(e)},r},t((function(t){n.reason||(n.reason=new r(t),e(n.reason))}))}o.prototype.throwIfRequested=function(){if(this.reason)throw this.reason},o.prototype.subscribe=function(t){this.reason?t(this.reason):this._listeners?this._listeners.push(t):this._listeners=[t]},o.prototype.unsubscribe=function(t){if(this._listeners){var e=this._listeners.indexOf(t);-1!==e&&this._listeners.splice(e,1)}},o.source=function(){var t;return{token:new o((function(e){t=e})),cancel:t}},t.exports=o},6502:t=>{"use strict";t.exports=function(t){return!(!t||!t.__CANCEL__)}},321:(t,e,n)=>{"use strict";var r=n(4867),o=n(5327),i=n(782),s=n(3572),a=n(7185),u=n(4875),c=u.validators;function l(t){this.defaults=t,this.interceptors={request:new i,response:new i}}l.prototype.request=function(t,e){"string"==typeof t?(e=e||{}).url=t:e=t||{},(e=a(this.defaults,e)).method?e.method=e.method.toLowerCase():this.defaults.method?e.method=this.defaults.method.toLowerCase():e.method="get";var n=e.transitional;void 0!==n&&u.assertOptions(n,{silentJSONParsing:c.transitional(c.boolean),forcedJSONParsing:c.transitional(c.boolean),clarifyTimeoutError:c.transitional(c.boolean)},!1);var r=[],o=!0;this.interceptors.request.forEach((function(t){"function"==typeof t.runWhen&&!1===t.runWhen(e)||(o=o&&t.synchronous,r.unshift(t.fulfilled,t.rejected))}));var i,l=[];if(this.interceptors.response.forEach((function(t){l.push(t.fulfilled,t.rejected)})),!o){var f=[s,void 0];for(Array.prototype.unshift.apply(f,r),f=f.concat(l),i=Promise.resolve(e);f.length;)i=i.then(f.shift(),f.shift());return i}for(var d=e;r.length;){var p=r.shift(),h=r.shift();try{d=p(d)}catch(t){h(t);break}}try{i=s(d)}catch(t){return Promise.reject(t)}for(;l.length;)i=i.then(l.shift(),l.shift());return i},l.prototype.getUri=function(t){return t=a(this.defaults,t),o(t.url,t.params,t.paramsSerializer).replace(/^\?/,"")},r.forEach(["delete","get","head","options"],(function(t){l.prototype[t]=function(e,n){return this.request(a(n||{},{method:t,url:e,data:(n||{}).data}))}})),r.forEach(["post","put","patch"],(function(t){l.prototype[t]=function(e,n,r){return this.request(a(r||{},{method:t,url:e,data:n}))}})),t.exports=l},782:(t,e,n)=>{"use strict";var r=n(4867);function o(){this.handlers=[]}o.prototype.use=function(t,e,n){return this.handlers.push({fulfilled:t,rejected:e,synchronous:!!n&&n.synchronous,runWhen:n?n.runWhen:null}),this.handlers.length-1},o.prototype.eject=function(t){this.handlers[t]&&(this.handlers[t]=null)},o.prototype.forEach=function(t){r.forEach(this.handlers,(function(e){null!==e&&t(e)}))},t.exports=o},4097:(t,e,n)=>{"use strict";var r=n(1793),o=n(7303);t.exports=function(t,e){return t&&!r(e)?o(t,e):e}},5061:(t,e,n)=>{"use strict";var r=n(481);t.exports=function(t,e,n,o,i){var s=new Error(t);return r(s,e,n,o,i)}},3572:(t,e,n)=>{"use strict";var r=n(4867),o=n(8527),i=n(6502),s=n(5655),a=n(4442);function u(t){if(t.cancelToken&&t.cancelToken.throwIfRequested(),t.signal&&t.signal.aborted)throw new a("canceled")}t.exports=function(t){return u(t),t.headers=t.headers||{},t.data=o.call(t,t.data,t.headers,t.transformRequest),t.headers=r.merge(t.headers.common||{},t.headers[t.method]||{},t.headers),r.forEach(["delete","get","head","post","put","patch","common"],(function(e){delete t.headers[e]})),(t.adapter||s.adapter)(t).then((function(e){return u(t),e.data=o.call(t,e.data,e.headers,t.transformResponse),e}),(function(e){return i(e)||(u(t),e&&e.response&&(e.response.data=o.call(t,e.response.data,e.response.headers,t.transformResponse))),Promise.reject(e)}))}},481:t=>{"use strict";t.exports=function(t,e,n,r,o){return t.config=e,n&&(t.code=n),t.request=r,t.response=o,t.isAxiosError=!0,t.toJSON=function(){return{message:this.message,name:this.name,description:this.description,number:this.number,fileName:this.fileName,lineNumber:this.lineNumber,columnNumber:this.columnNumber,stack:this.stack,config:this.config,code:this.code,status:this.response&&this.response.status?this.response.status:null}},t}},7185:(t,e,n)=>{"use strict";var r=n(4867);t.exports=function(t,e){e=e||{};var n={};function o(t,e){return r.isPlainObject(t)&&r.isPlainObject(e)?r.merge(t,e):r.isPlainObject(e)?r.merge({},e):r.isArray(e)?e.slice():e}function i(n){return r.isUndefined(e[n])?r.isUndefined(t[n])?void 0:o(void 0,t[n]):o(t[n],e[n])}function s(t){if(!r.isUndefined(e[t]))return o(void 0,e[t])}function a(n){return r.isUndefined(e[n])?r.isUndefined(t[n])?void 0:o(void 0,t[n]):o(void 0,e[n])}function u(n){return n in e?o(t[n],e[n]):n in t?o(void 0,t[n]):void 0}var c={url:s,method:s,data:s,baseURL:a,transformRequest:a,transformResponse:a,paramsSerializer:a,timeout:a,timeoutMessage:a,withCredentials:a,adapter:a,responseType:a,xsrfCookieName:a,xsrfHeaderName:a,onUploadProgress:a,onDownloadProgress:a,decompress:a,maxContentLength:a,maxBodyLength:a,transport:a,httpAgent:a,httpsAgent:a,cancelToken:a,socketPath:a,responseEncoding:a,validateStatus:u};return r.forEach(Object.keys(t).concat(Object.keys(e)),(function(t){var e=c[t]||i,o=e(t);r.isUndefined(o)&&e!==u||(n[t]=o)})),n}},6026:(t,e,n)=>{"use strict";var r=n(5061);t.exports=function(t,e,n){var o=n.config.validateStatus;n.status&&o&&!o(n.status)?e(r("Request failed with status code "+n.status,n.config,null,n.request,n)):t(n)}},8527:(t,e,n)=>{"use strict";var r=n(4867),o=n(5655);t.exports=function(t,e,n){var i=this||o;return r.forEach(n,(function(n){t=n.call(i,t,e)})),t}},5655:(t,e,n)=>{"use strict";var r=n(4155),o=n(4867),i=n(6016),s=n(481),a={"Content-Type":"application/x-www-form-urlencoded"};function u(t,e){!o.isUndefined(t)&&o.isUndefined(t["Content-Type"])&&(t["Content-Type"]=e)}var c,l={transitional:{silentJSONParsing:!0,forcedJSONParsing:!0,clarifyTimeoutError:!1},adapter:(("undefined"!=typeof XMLHttpRequest||void 0!==r&&"[object process]"===Object.prototype.toString.call(r))&&(c=n(5448)),c),transformRequest:[function(t,e){return i(e,"Accept"),i(e,"Content-Type"),o.isFormData(t)||o.isArrayBuffer(t)||o.isBuffer(t)||o.isStream(t)||o.isFile(t)||o.isBlob(t)?t:o.isArrayBufferView(t)?t.buffer:o.isURLSearchParams(t)?(u(e,"application/x-www-form-urlencoded;charset=utf-8"),t.toString()):o.isObject(t)||e&&"application/json"===e["Content-Type"]?(u(e,"application/json"),function(t,e,n){if(o.isString(t))try{return(e||JSON.parse)(t),o.trim(t)}catch(t){if("SyntaxError"!==t.name)throw t}return(n||JSON.stringify)(t)}(t)):t}],transformResponse:[function(t){var e=this.transitional||l.transitional,n=e&&e.silentJSONParsing,r=e&&e.forcedJSONParsing,i=!n&&"json"===this.responseType;if(i||r&&o.isString(t)&&t.length)try{return JSON.parse(t)}catch(t){if(i){if("SyntaxError"===t.name)throw s(t,this,"E_JSON_PARSE");throw t}}return t}],timeout:0,xsrfCookieName:"XSRF-TOKEN",xsrfHeaderName:"X-XSRF-TOKEN",maxContentLength:-1,maxBodyLength:-1,validateStatus:function(t){return t>=200&&t<300},headers:{common:{Accept:"application/json, text/plain, */*"}}};o.forEach(["delete","get","head"],(function(t){l.headers[t]={}})),o.forEach(["post","put","patch"],(function(t){l.headers[t]=o.merge(a)})),t.exports=l},7288:t=>{t.exports={version:"0.26.0"}},1849:t=>{"use strict";t.exports=function(t,e){return function(){for(var n=new Array(arguments.length),r=0;r<n.length;r++)n[r]=arguments[r];return t.apply(e,n)}}},5327:(t,e,n)=>{"use strict";var r=n(4867);function o(t){return encodeURIComponent(t).replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"+").replace(/%5B/gi,"[").replace(/%5D/gi,"]")}t.exports=function(t,e,n){if(!e)return t;var i;if(n)i=n(e);else if(r.isURLSearchParams(e))i=e.toString();else{var s=[];r.forEach(e,(function(t,e){null!=t&&(r.isArray(t)?e+="[]":t=[t],r.forEach(t,(function(t){r.isDate(t)?t=t.toISOString():r.isObject(t)&&(t=JSON.stringify(t)),s.push(o(e)+"="+o(t))})))})),i=s.join("&")}if(i){var a=t.indexOf("#");-1!==a&&(t=t.slice(0,a)),t+=(-1===t.indexOf("?")?"?":"&")+i}return t}},7303:t=>{"use strict";t.exports=function(t,e){return e?t.replace(/\/+$/,"")+"/"+e.replace(/^\/+/,""):t}},4372:(t,e,n)=>{"use strict";var r=n(4867);t.exports=r.isStandardBrowserEnv()?{write:function(t,e,n,o,i,s){var a=[];a.push(t+"="+encodeURIComponent(e)),r.isNumber(n)&&a.push("expires="+new Date(n).toGMTString()),r.isString(o)&&a.push("path="+o),r.isString(i)&&a.push("domain="+i),!0===s&&a.push("secure"),document.cookie=a.join("; ")},read:function(t){var e=document.cookie.match(new RegExp("(^|;\\s*)("+t+")=([^;]*)"));return e?decodeURIComponent(e[3]):null},remove:function(t){this.write(t,"",Date.now()-864e5)}}:{write:function(){},read:function(){return null},remove:function(){}}},1793:t=>{"use strict";t.exports=function(t){return/^([a-z][a-z\d+\-.]*:)?\/\//i.test(t)}},6268:(t,e,n)=>{"use strict";var r=n(4867);t.exports=function(t){return r.isObject(t)&&!0===t.isAxiosError}},7985:(t,e,n)=>{"use strict";var r=n(4867);t.exports=r.isStandardBrowserEnv()?function(){var t,e=/(msie|trident)/i.test(navigator.userAgent),n=document.createElement("a");function o(t){var r=t;return e&&(n.setAttribute("href",r),r=n.href),n.setAttribute("href",r),{href:n.href,protocol:n.protocol?n.protocol.replace(/:$/,""):"",host:n.host,search:n.search?n.search.replace(/^\?/,""):"",hash:n.hash?n.hash.replace(/^#/,""):"",hostname:n.hostname,port:n.port,pathname:"/"===n.pathname.charAt(0)?n.pathname:"/"+n.pathname}}return t=o(window.location.href),function(e){var n=r.isString(e)?o(e):e;return n.protocol===t.protocol&&n.host===t.host}}():function(){return!0}},6016:(t,e,n)=>{"use strict";var r=n(4867);t.exports=function(t,e){r.forEach(t,(function(n,r){r!==e&&r.toUpperCase()===e.toUpperCase()&&(t[e]=n,delete t[r])}))}},4109:(t,e,n)=>{"use strict";var r=n(4867),o=["age","authorization","content-length","content-type","etag","expires","from","host","if-modified-since","if-unmodified-since","last-modified","location","max-forwards","proxy-authorization","referer","retry-after","user-agent"];t.exports=function(t){var e,n,i,s={};return t?(r.forEach(t.split("\n"),(function(t){if(i=t.indexOf(":"),e=r.trim(t.substr(0,i)).toLowerCase(),n=r.trim(t.substr(i+1)),e){if(s[e]&&o.indexOf(e)>=0)return;s[e]="set-cookie"===e?(s[e]?s[e]:[]).concat([n]):s[e]?s[e]+", "+n:n}})),s):s}},8713:t=>{"use strict";t.exports=function(t){return function(e){return t.apply(null,e)}}},4875:(t,e,n)=>{"use strict";var r=n(7288).version,o={};["object","boolean","number","function","string","symbol"].forEach((function(t,e){o[t]=function(n){return typeof n===t||"a"+(e<1?"n ":" ")+t}}));var i={};o.transitional=function(t,e,n){function o(t,e){return"[Axios v"+r+"] Transitional option '"+t+"'"+e+(n?". "+n:"")}return function(n,r,s){if(!1===t)throw new Error(o(r," has been removed"+(e?" in "+e:"")));return e&&!i[r]&&(i[r]=!0,console.warn(o(r," has been deprecated since v"+e+" and will be removed in the near future"))),!t||t(n,r,s)}},t.exports={assertOptions:function(t,e,n){if("object"!=typeof t)throw new TypeError("options must be an object");for(var r=Object.keys(t),o=r.length;o-- >0;){var i=r[o],s=e[i];if(s){var a=t[i],u=void 0===a||s(a,i,t);if(!0!==u)throw new TypeError("option "+i+" must be "+u)}else if(!0!==n)throw Error("Unknown option "+i)}},validators:o}},4867:(t,e,n)=>{"use strict";var r=n(1849),o=Object.prototype.toString;function i(t){return Array.isArray(t)}function s(t){return void 0===t}function a(t){return"[object ArrayBuffer]"===o.call(t)}function u(t){return null!==t&&"object"==typeof t}function c(t){if("[object Object]"!==o.call(t))return!1;var e=Object.getPrototypeOf(t);return null===e||e===Object.prototype}function l(t){return"[object Function]"===o.call(t)}function f(t,e){if(null!=t)if("object"!=typeof t&&(t=[t]),i(t))for(var n=0,r=t.length;n<r;n++)e.call(null,t[n],n,t);else for(var o in t)Object.prototype.hasOwnProperty.call(t,o)&&e.call(null,t[o],o,t)}t.exports={isArray:i,isArrayBuffer:a,isBuffer:function(t){return null!==t&&!s(t)&&null!==t.constructor&&!s(t.constructor)&&"function"==typeof t.constructor.isBuffer&&t.constructor.isBuffer(t)},isFormData:function(t){return"[object FormData]"===o.call(t)},isArrayBufferView:function(t){return"undefined"!=typeof ArrayBuffer&&ArrayBuffer.isView?ArrayBuffer.isView(t):t&&t.buffer&&a(t.buffer)},isString:function(t){return"string"==typeof t},isNumber:function(t){return"number"==typeof t},isObject:u,isPlainObject:c,isUndefined:s,isDate:function(t){return"[object Date]"===o.call(t)},isFile:function(t){return"[object File]"===o.call(t)},isBlob:function(t){return"[object Blob]"===o.call(t)},isFunction:l,isStream:function(t){return u(t)&&l(t.pipe)},isURLSearchParams:function(t){return"[object URLSearchParams]"===o.call(t)},isStandardBrowserEnv:function(){return("undefined"==typeof navigator||"ReactNative"!==navigator.product&&"NativeScript"!==navigator.product&&"NS"!==navigator.product)&&("undefined"!=typeof window&&"undefined"!=typeof document)},forEach:f,merge:function t(){var e={};function n(n,r){c(e[r])&&c(n)?e[r]=t(e[r],n):c(n)?e[r]=t({},n):i(n)?e[r]=n.slice():e[r]=n}for(var r=0,o=arguments.length;r<o;r++)f(arguments[r],n);return e},extend:function(t,e,n){return f(e,(function(e,o){t[o]=n&&"function"==typeof e?r(e,n):e})),t},trim:function(t){return t.trim?t.trim():t.replace(/^\s+|\s+$/g,"")},stripBOM:function(t){return 65279===t.charCodeAt(0)&&(t=t.slice(1)),t}}},9409:(t,e,n)=>{"use strict";n.r(e),n.d(e,{default:()=>l});var r=n(9669),o=n.n(r),i=n(6118);function s(t,e){var n="undefined"!=typeof Symbol&&t[Symbol.iterator]||t["@@iterator"];if(!n){if(Array.isArray(t)||(n=function(t,e){if(!t)return;if("string"==typeof t)return a(t,e);var n=Object.prototype.toString.call(t).slice(8,-1);"Object"===n&&t.constructor&&(n=t.constructor.name);if("Map"===n||"Set"===n)return Array.from(t);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return a(t,e)}(t))||e&&t&&"number"==typeof t.length){n&&(t=n);var r=0,o=function(){};return{s:o,n:function(){return r>=t.length?{done:!0}:{done:!1,value:t[r++]}},e:function(t){throw t},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var i,s=!0,u=!1;return{s:function(){n=n.call(t)},n:function(){var t=n.next();return s=t.done,t},e:function(t){u=!0,i=t},f:function(){try{s||null==n.return||n.return()}finally{if(u)throw i}}}}function a(t,e){(null==e||e>t.length)&&(e=t.length);for(var n=0,r=new Array(e);n<e;n++)r[n]=t[n];return r}const u={data:function(){return{titulo:"Orden",total:0,monto:0,sending:!1}},props:{productos:Array,item:Object,id:String,isVenta:Boolean},components:{LoadingButton:i.Z},methods:{getProduct:function(t){var e,n={},r=s(this.productos);try{for(r.s();!(e=r.n()).done;){var o=e.value;if(o.id==t){n=o;break}}}catch(t){r.e(t)}finally{r.f()}return n?n.formato+" ("+n.dimension+")":""},guardar:function(t){var e=this;this.sending=!0,this.reajusteOrden();var n=new FormData;n.append("item",JSON.stringify(this.item)),this.monto&&n.append("monto",this.monto),o().post(t,n,{headers:{"Content-Type":"multipart/form-data"}}).then((function(t){var n=t.data;if(0==n.status)e.$bvModal.hide(e.id),e.$inertia.get(n.path);else for(var r in e.form)r in n.errors?(e.form[r].state=!1,e.form[r].stateText=n.errors[r][0]):(e.form[r].state=!0,e.form[r].stateText="")})).catch((function(t){e.errors=t,console.log(t)})).finally((function(){e.sending=!1}))},guardarVenta:function(){this.guardar("/ordenVenta")},guardarDeuda:function(){this.guardar("/ordenDeuda")},getTotal:function(t){var e=0;if(t){var n,r=s(t);try{for(r.s();!(n=r.n()).done;){var o=n.value;o&&(e+=o.costo*o.cantidad)}}catch(t){r.e(t)}finally{r.f()}this.total=e}return e},getCambio:function(){return this.item.montoVenta-this.total},getSaldo:function(){return this.total-this.item.montoVenta-this.monto},getObservaciones:function(t){return t?t.replace(/\n/g,"<br/>"):""},reajusteOrden:function(){this.item.montoVenta>this.total&&(this.item.montoVenta=this.total)},getUrlPrint:function(t){return this.isVenta?"/ordenPdfV/"+t:"/ordenPdf/"+t}}},c=u;const l=(0,n(1900).Z)(c,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("b-modal",{attrs:{id:t.id,title:t.titulo+" "+(t.item.codigoServicio?t.item.codigoServicio:t.item.correlativo)},scopedSlots:t._u([{key:"modal-footer",fn:function(e){e.ok;var r=e.cancel;return[n("b-button",{attrs:{size:"sm",variant:"danger"},on:{click:function(t){return r()}}},[t._v("\n            Cerrar\n        ")]),t._v(" "),n("a",{staticClass:"btn btn-outline-primary btn-sm",attrs:{href:t.getUrlPrint(t.item.id),target:"_blank"}},[t._v("Imprimir")]),t._v(" "),t.isVenta&&[1,5].includes(t.item.estado)?n("loading-button",{attrs:{loading:t.sending,variant:"primary",size:"sm",text:"Guardar",textLoad:"Guardando"},nativeOn:{click:function(e){return t.guardarVenta()}}},[t._v("Guardar\n        ")]):t._e(),t._v(" "),t.isVenta&&2==t.item.estado?n("loading-button",{attrs:{loading:t.sending,variant:"primary",size:"sm",text:"Pagar",textLoad:"Pagando"},nativeOn:{click:function(e){return t.guardarDeuda()}}},[t._v("Pagar\n        ")]):t._e()]}}])},[n("div",[n("strong",[t._v("Cliente:")]),t._v(" "+t._s(t.item.responsable))]),t._v(" "),n("div",[n("strong",[t._v("Telefono:")]),t._v(" "+t._s(t.item.telefono))]),t._v(" "),n("br"),t._v(" "),n("div",{staticClass:"table-responsive"},[n("table",{staticClass:"table"},[n("thead",[n("tr",[n("th",{attrs:{scope:"col"}},[t._v("#")]),t._v(" "),n("th",{attrs:{scope:"col"}},[t._v("Productos")]),t._v(" "),n("th",{attrs:{scope:"col"}},[t._v("Cant.")]),t._v(" "),t.isVenta?n("th",{attrs:{scope:"col"}},[t._v("Precio")]):t._e(),t._v(" "),t.isVenta?n("th",{attrs:{scope:"col"}},[t._v("Total")]):t._e()])]),t._v(" "),n("tbody",t._l(t.item.detallesOrden,(function(e,r){return n("tr",[n("td",[t._v(t._s(r+1))]),t._v(" "),n("td",[t._v(t._s(t.getProduct(e.stock)))]),t._v(" "),n("td",[t._v(t._s(e.cantidad))]),t._v(" "),t.isVenta?n("td",[[1,5].includes(t.item.estado)?n("b-input",{attrs:{type:"text",size:"sm"},model:{value:e.costo,callback:function(n){t.$set(e,"costo",n)},expression:"itemOrden.costo"}}):[t._v(t._s(e.costo))]],2):t._e(),t._v(" "),t.isVenta?n("td",[t._v(t._s(e.costo*e.cantidad))]):t._e()])})),0),t._v(" "),t.isVenta?n("tfoot",[n("tr",[n("td",{staticClass:"text-right",attrs:{colspan:"4"}},[n("strong",[t._v("Total")])]),t._v(" "),n("td",[t._v(t._s(t.getTotal(t.item.detallesOrden)))])]),t._v(" "),[1,5].includes(t.item.estado)?[n("tr",[n("td",{staticClass:"text-right",attrs:{colspan:"4"}},[n("strong",[t._v("Cancelado")])]),t._v(" "),n("td",[[1,5].includes(t.item.estado)?n("b-input",{attrs:{type:"text",size:"sm"},model:{value:t.item.montoVenta,callback:function(e){t.$set(t.item,"montoVenta",e)},expression:"item.montoVenta"}}):[t._v(t._s(t.item.montoVenta))]],2)]),t._v(" "),n("tr",[n("td",{staticClass:"text-right",attrs:{colspan:"4"}},[n("strong",[t._v("Cambio")])]),t._v(" "),n("td",[t._v(t._s(t.getCambio()))])])]:t._e()],2):t._e()])]),t._v(" "),2==t.item.estado?n("div",[n("form",[n("b-row",[n("b-col",[n("b-form-group",{attrs:{label:"Cancelado","label-for":"cancelado"}},[n("b-input",{attrs:{type:"text",placeholder:"Cancelado",id:"cancelado",name:"cancelado",disabled:""},model:{value:t.item.montoVenta,callback:function(e){t.$set(t.item,"montoVenta",e)},expression:"item.montoVenta"}})],1)],1),t._v(" "),n("b-col",[n("b-form-group",{attrs:{label:"A Cuenta","label-for":"cuenta"}},[n("b-input",{attrs:{type:"text",placeholder:"Cuenta",id:"cuenta",name:"cuenta"},model:{value:t.monto,callback:function(e){t.monto=e},expression:"monto"}})],1)],1),t._v(" "),n("b-col",[n("b-form-group",{attrs:{label:"Saldo","label-for":"saldo"}},[n("b-input",{attrs:{type:"text",placeholder:"Saldo",value:t.getSaldo(),id:"saldo",name:"saldo",disabled:""}})],1)],1)],1)],1)]):t._e(),t._v(" "),n("div",[n("p",[n("strong",[t._v("Observaciones:")]),n("br"),t._v(" "),n("span",{domProps:{innerHTML:t._s(t.getObservaciones(t.item.observaciones))}})])])])}),[],!1,null,null,null).exports},6118:(t,e,n)=>{"use strict";n.d(e,{Z:()=>o});const r={props:{loading:Boolean,text:String,textLoad:String,variant:String,size:String}};const o=(0,n(1900).Z)(r,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("b-button",{attrs:{variant:t.variant?t.variant:"primary",size:t.size?t.size:"",disabled:t.loading}},[t.loading?n("span",[n("b-spinner",{attrs:{small:""}}),t._v("\n        "+t._s(t.textLoad)+"...\n      ")],1):n("span",[t._v("\n        "+t._s(t.text)+"\n      ")])])}),[],!1,null,null,null).exports},1900:(t,e,n)=>{"use strict";function r(t,e,n,r,o,i,s,a){var u,c="function"==typeof t?t.options:t;if(e&&(c.render=e,c.staticRenderFns=n,c._compiled=!0),r&&(c.functional=!0),i&&(c._scopeId="data-v-"+i),s?(u=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),o&&o.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(s)},c._ssrRegister=u):o&&(u=a?function(){o.call(this,(c.functional?this.parent:this).$root.$options.shadowRoot)}:o),u)if(c.functional){c._injectStyles=u;var l=c.render;c.render=function(t,e){return u.call(e),l(t,e)}}else{var f=c.beforeCreate;c.beforeCreate=f?[].concat(f,u):[u]}return{exports:t,options:c}}n.d(e,{Z:()=>r})}}]);