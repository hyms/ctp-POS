(self.webpackChunk=self.webpackChunk||[]).push([[798],{9669:(e,t,r)=>{e.exports=r(1609)},5448:(e,t,r)=>{"use strict";var n=r(4867),o=r(6026),s=r(4372),i=r(5327),a=r(4097),u=r(4109),c=r(7985),l=r(5061),f=r(5655),d=r(4442);e.exports=function(e){return new Promise((function(t,r){var p,h=e.data,m=e.headers,v=e.responseType;function g(){e.cancelToken&&e.cancelToken.unsubscribe(p),e.signal&&e.signal.removeEventListener("abort",p)}n.isFormData(h)&&delete m["Content-Type"];var b=new XMLHttpRequest;if(e.auth){var y=e.auth.username||"",w=e.auth.password?unescape(encodeURIComponent(e.auth.password)):"";m.Authorization="Basic "+btoa(y+":"+w)}var x=a(e.baseURL,e.url);function C(){if(b){var n="getAllResponseHeaders"in b?u(b.getAllResponseHeaders()):null,s={data:v&&"text"!==v&&"json"!==v?b.response:b.responseText,status:b.status,statusText:b.statusText,headers:n,config:e,request:b};o((function(e){t(e),g()}),(function(e){r(e),g()}),s),b=null}}if(b.open(e.method.toUpperCase(),i(x,e.params,e.paramsSerializer),!0),b.timeout=e.timeout,"onloadend"in b?b.onloadend=C:b.onreadystatechange=function(){b&&4===b.readyState&&(0!==b.status||b.responseURL&&0===b.responseURL.indexOf("file:"))&&setTimeout(C)},b.onabort=function(){b&&(r(l("Request aborted",e,"ECONNABORTED",b)),b=null)},b.onerror=function(){r(l("Network Error",e,null,b)),b=null},b.ontimeout=function(){var t=e.timeout?"timeout of "+e.timeout+"ms exceeded":"timeout exceeded",n=e.transitional||f.transitional;e.timeoutErrorMessage&&(t=e.timeoutErrorMessage),r(l(t,e,n.clarifyTimeoutError?"ETIMEDOUT":"ECONNABORTED",b)),b=null},n.isStandardBrowserEnv()){var _=(e.withCredentials||c(x))&&e.xsrfCookieName?s.read(e.xsrfCookieName):void 0;_&&(m[e.xsrfHeaderName]=_)}"setRequestHeader"in b&&n.forEach(m,(function(e,t){void 0===h&&"content-type"===t.toLowerCase()?delete m[t]:b.setRequestHeader(t,e)})),n.isUndefined(e.withCredentials)||(b.withCredentials=!!e.withCredentials),v&&"json"!==v&&(b.responseType=e.responseType),"function"==typeof e.onDownloadProgress&&b.addEventListener("progress",e.onDownloadProgress),"function"==typeof e.onUploadProgress&&b.upload&&b.upload.addEventListener("progress",e.onUploadProgress),(e.cancelToken||e.signal)&&(p=function(e){b&&(r(!e||e&&e.type?new d("canceled"):e),b.abort(),b=null)},e.cancelToken&&e.cancelToken.subscribe(p),e.signal&&(e.signal.aborted?p():e.signal.addEventListener("abort",p))),h||(h=null),b.send(h)}))}},1609:(e,t,r)=>{"use strict";var n=r(4867),o=r(1849),s=r(321),i=r(7185);var a=function e(t){var r=new s(t),a=o(s.prototype.request,r);return n.extend(a,s.prototype,r),n.extend(a,r),a.create=function(r){return e(i(t,r))},a}(r(5655));a.Axios=s,a.Cancel=r(4442),a.CancelToken=r(4972),a.isCancel=r(6502),a.VERSION=r(7288).version,a.all=function(e){return Promise.all(e)},a.spread=r(8713),a.isAxiosError=r(6268),e.exports=a,e.exports.default=a},4442:e=>{"use strict";function t(e){this.message=e}t.prototype.toString=function(){return"Cancel"+(this.message?": "+this.message:"")},t.prototype.__CANCEL__=!0,e.exports=t},4972:(e,t,r)=>{"use strict";var n=r(4442);function o(e){if("function"!=typeof e)throw new TypeError("executor must be a function.");var t;this.promise=new Promise((function(e){t=e}));var r=this;this.promise.then((function(e){if(r._listeners){var t,n=r._listeners.length;for(t=0;t<n;t++)r._listeners[t](e);r._listeners=null}})),this.promise.then=function(e){var t,n=new Promise((function(e){r.subscribe(e),t=e})).then(e);return n.cancel=function(){r.unsubscribe(t)},n},e((function(e){r.reason||(r.reason=new n(e),t(r.reason))}))}o.prototype.throwIfRequested=function(){if(this.reason)throw this.reason},o.prototype.subscribe=function(e){this.reason?e(this.reason):this._listeners?this._listeners.push(e):this._listeners=[e]},o.prototype.unsubscribe=function(e){if(this._listeners){var t=this._listeners.indexOf(e);-1!==t&&this._listeners.splice(t,1)}},o.source=function(){var e;return{token:new o((function(t){e=t})),cancel:e}},e.exports=o},6502:e=>{"use strict";e.exports=function(e){return!(!e||!e.__CANCEL__)}},321:(e,t,r)=>{"use strict";var n=r(4867),o=r(5327),s=r(782),i=r(3572),a=r(7185),u=r(4875),c=u.validators;function l(e){this.defaults=e,this.interceptors={request:new s,response:new s}}l.prototype.request=function(e,t){"string"==typeof e?(t=t||{}).url=e:t=e||{},(t=a(this.defaults,t)).method?t.method=t.method.toLowerCase():this.defaults.method?t.method=this.defaults.method.toLowerCase():t.method="get";var r=t.transitional;void 0!==r&&u.assertOptions(r,{silentJSONParsing:c.transitional(c.boolean),forcedJSONParsing:c.transitional(c.boolean),clarifyTimeoutError:c.transitional(c.boolean)},!1);var n=[],o=!0;this.interceptors.request.forEach((function(e){"function"==typeof e.runWhen&&!1===e.runWhen(t)||(o=o&&e.synchronous,n.unshift(e.fulfilled,e.rejected))}));var s,l=[];if(this.interceptors.response.forEach((function(e){l.push(e.fulfilled,e.rejected)})),!o){var f=[i,void 0];for(Array.prototype.unshift.apply(f,n),f=f.concat(l),s=Promise.resolve(t);f.length;)s=s.then(f.shift(),f.shift());return s}for(var d=t;n.length;){var p=n.shift(),h=n.shift();try{d=p(d)}catch(e){h(e);break}}try{s=i(d)}catch(e){return Promise.reject(e)}for(;l.length;)s=s.then(l.shift(),l.shift());return s},l.prototype.getUri=function(e){return e=a(this.defaults,e),o(e.url,e.params,e.paramsSerializer).replace(/^\?/,"")},n.forEach(["delete","get","head","options"],(function(e){l.prototype[e]=function(t,r){return this.request(a(r||{},{method:e,url:t,data:(r||{}).data}))}})),n.forEach(["post","put","patch"],(function(e){l.prototype[e]=function(t,r,n){return this.request(a(n||{},{method:e,url:t,data:r}))}})),e.exports=l},782:(e,t,r)=>{"use strict";var n=r(4867);function o(){this.handlers=[]}o.prototype.use=function(e,t,r){return this.handlers.push({fulfilled:e,rejected:t,synchronous:!!r&&r.synchronous,runWhen:r?r.runWhen:null}),this.handlers.length-1},o.prototype.eject=function(e){this.handlers[e]&&(this.handlers[e]=null)},o.prototype.forEach=function(e){n.forEach(this.handlers,(function(t){null!==t&&e(t)}))},e.exports=o},4097:(e,t,r)=>{"use strict";var n=r(1793),o=r(7303);e.exports=function(e,t){return e&&!n(t)?o(e,t):t}},5061:(e,t,r)=>{"use strict";var n=r(481);e.exports=function(e,t,r,o,s){var i=new Error(e);return n(i,t,r,o,s)}},3572:(e,t,r)=>{"use strict";var n=r(4867),o=r(8527),s=r(6502),i=r(5655),a=r(4442);function u(e){if(e.cancelToken&&e.cancelToken.throwIfRequested(),e.signal&&e.signal.aborted)throw new a("canceled")}e.exports=function(e){return u(e),e.headers=e.headers||{},e.data=o.call(e,e.data,e.headers,e.transformRequest),e.headers=n.merge(e.headers.common||{},e.headers[e.method]||{},e.headers),n.forEach(["delete","get","head","post","put","patch","common"],(function(t){delete e.headers[t]})),(e.adapter||i.adapter)(e).then((function(t){return u(e),t.data=o.call(e,t.data,t.headers,e.transformResponse),t}),(function(t){return s(t)||(u(e),t&&t.response&&(t.response.data=o.call(e,t.response.data,t.response.headers,e.transformResponse))),Promise.reject(t)}))}},481:e=>{"use strict";e.exports=function(e,t,r,n,o){return e.config=t,r&&(e.code=r),e.request=n,e.response=o,e.isAxiosError=!0,e.toJSON=function(){return{message:this.message,name:this.name,description:this.description,number:this.number,fileName:this.fileName,lineNumber:this.lineNumber,columnNumber:this.columnNumber,stack:this.stack,config:this.config,code:this.code,status:this.response&&this.response.status?this.response.status:null}},e}},7185:(e,t,r)=>{"use strict";var n=r(4867);e.exports=function(e,t){t=t||{};var r={};function o(e,t){return n.isPlainObject(e)&&n.isPlainObject(t)?n.merge(e,t):n.isPlainObject(t)?n.merge({},t):n.isArray(t)?t.slice():t}function s(r){return n.isUndefined(t[r])?n.isUndefined(e[r])?void 0:o(void 0,e[r]):o(e[r],t[r])}function i(e){if(!n.isUndefined(t[e]))return o(void 0,t[e])}function a(r){return n.isUndefined(t[r])?n.isUndefined(e[r])?void 0:o(void 0,e[r]):o(void 0,t[r])}function u(r){return r in t?o(e[r],t[r]):r in e?o(void 0,e[r]):void 0}var c={url:i,method:i,data:i,baseURL:a,transformRequest:a,transformResponse:a,paramsSerializer:a,timeout:a,timeoutMessage:a,withCredentials:a,adapter:a,responseType:a,xsrfCookieName:a,xsrfHeaderName:a,onUploadProgress:a,onDownloadProgress:a,decompress:a,maxContentLength:a,maxBodyLength:a,transport:a,httpAgent:a,httpsAgent:a,cancelToken:a,socketPath:a,responseEncoding:a,validateStatus:u};return n.forEach(Object.keys(e).concat(Object.keys(t)),(function(e){var t=c[e]||s,o=t(e);n.isUndefined(o)&&t!==u||(r[e]=o)})),r}},6026:(e,t,r)=>{"use strict";var n=r(5061);e.exports=function(e,t,r){var o=r.config.validateStatus;r.status&&o&&!o(r.status)?t(n("Request failed with status code "+r.status,r.config,null,r.request,r)):e(r)}},8527:(e,t,r)=>{"use strict";var n=r(4867),o=r(5655);e.exports=function(e,t,r){var s=this||o;return n.forEach(r,(function(r){e=r.call(s,e,t)})),e}},5655:(e,t,r)=>{"use strict";var n=r(4155),o=r(4867),s=r(6016),i=r(481),a={"Content-Type":"application/x-www-form-urlencoded"};function u(e,t){!o.isUndefined(e)&&o.isUndefined(e["Content-Type"])&&(e["Content-Type"]=t)}var c,l={transitional:{silentJSONParsing:!0,forcedJSONParsing:!0,clarifyTimeoutError:!1},adapter:(("undefined"!=typeof XMLHttpRequest||void 0!==n&&"[object process]"===Object.prototype.toString.call(n))&&(c=r(5448)),c),transformRequest:[function(e,t){return s(t,"Accept"),s(t,"Content-Type"),o.isFormData(e)||o.isArrayBuffer(e)||o.isBuffer(e)||o.isStream(e)||o.isFile(e)||o.isBlob(e)?e:o.isArrayBufferView(e)?e.buffer:o.isURLSearchParams(e)?(u(t,"application/x-www-form-urlencoded;charset=utf-8"),e.toString()):o.isObject(e)||t&&"application/json"===t["Content-Type"]?(u(t,"application/json"),function(e,t,r){if(o.isString(e))try{return(t||JSON.parse)(e),o.trim(e)}catch(e){if("SyntaxError"!==e.name)throw e}return(r||JSON.stringify)(e)}(e)):e}],transformResponse:[function(e){var t=this.transitional||l.transitional,r=t&&t.silentJSONParsing,n=t&&t.forcedJSONParsing,s=!r&&"json"===this.responseType;if(s||n&&o.isString(e)&&e.length)try{return JSON.parse(e)}catch(e){if(s){if("SyntaxError"===e.name)throw i(e,this,"E_JSON_PARSE");throw e}}return e}],timeout:0,xsrfCookieName:"XSRF-TOKEN",xsrfHeaderName:"X-XSRF-TOKEN",maxContentLength:-1,maxBodyLength:-1,validateStatus:function(e){return e>=200&&e<300},headers:{common:{Accept:"application/json, text/plain, */*"}}};o.forEach(["delete","get","head"],(function(e){l.headers[e]={}})),o.forEach(["post","put","patch"],(function(e){l.headers[e]=o.merge(a)})),e.exports=l},7288:e=>{e.exports={version:"0.26.0"}},1849:e=>{"use strict";e.exports=function(e,t){return function(){for(var r=new Array(arguments.length),n=0;n<r.length;n++)r[n]=arguments[n];return e.apply(t,r)}}},5327:(e,t,r)=>{"use strict";var n=r(4867);function o(e){return encodeURIComponent(e).replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"+").replace(/%5B/gi,"[").replace(/%5D/gi,"]")}e.exports=function(e,t,r){if(!t)return e;var s;if(r)s=r(t);else if(n.isURLSearchParams(t))s=t.toString();else{var i=[];n.forEach(t,(function(e,t){null!=e&&(n.isArray(e)?t+="[]":e=[e],n.forEach(e,(function(e){n.isDate(e)?e=e.toISOString():n.isObject(e)&&(e=JSON.stringify(e)),i.push(o(t)+"="+o(e))})))})),s=i.join("&")}if(s){var a=e.indexOf("#");-1!==a&&(e=e.slice(0,a)),e+=(-1===e.indexOf("?")?"?":"&")+s}return e}},7303:e=>{"use strict";e.exports=function(e,t){return t?e.replace(/\/+$/,"")+"/"+t.replace(/^\/+/,""):e}},4372:(e,t,r)=>{"use strict";var n=r(4867);e.exports=n.isStandardBrowserEnv()?{write:function(e,t,r,o,s,i){var a=[];a.push(e+"="+encodeURIComponent(t)),n.isNumber(r)&&a.push("expires="+new Date(r).toGMTString()),n.isString(o)&&a.push("path="+o),n.isString(s)&&a.push("domain="+s),!0===i&&a.push("secure"),document.cookie=a.join("; ")},read:function(e){var t=document.cookie.match(new RegExp("(^|;\\s*)("+e+")=([^;]*)"));return t?decodeURIComponent(t[3]):null},remove:function(e){this.write(e,"",Date.now()-864e5)}}:{write:function(){},read:function(){return null},remove:function(){}}},1793:e=>{"use strict";e.exports=function(e){return/^([a-z][a-z\d+\-.]*:)?\/\//i.test(e)}},6268:(e,t,r)=>{"use strict";var n=r(4867);e.exports=function(e){return n.isObject(e)&&!0===e.isAxiosError}},7985:(e,t,r)=>{"use strict";var n=r(4867);e.exports=n.isStandardBrowserEnv()?function(){var e,t=/(msie|trident)/i.test(navigator.userAgent),r=document.createElement("a");function o(e){var n=e;return t&&(r.setAttribute("href",n),n=r.href),r.setAttribute("href",n),{href:r.href,protocol:r.protocol?r.protocol.replace(/:$/,""):"",host:r.host,search:r.search?r.search.replace(/^\?/,""):"",hash:r.hash?r.hash.replace(/^#/,""):"",hostname:r.hostname,port:r.port,pathname:"/"===r.pathname.charAt(0)?r.pathname:"/"+r.pathname}}return e=o(window.location.href),function(t){var r=n.isString(t)?o(t):t;return r.protocol===e.protocol&&r.host===e.host}}():function(){return!0}},6016:(e,t,r)=>{"use strict";var n=r(4867);e.exports=function(e,t){n.forEach(e,(function(r,n){n!==t&&n.toUpperCase()===t.toUpperCase()&&(e[t]=r,delete e[n])}))}},4109:(e,t,r)=>{"use strict";var n=r(4867),o=["age","authorization","content-length","content-type","etag","expires","from","host","if-modified-since","if-unmodified-since","last-modified","location","max-forwards","proxy-authorization","referer","retry-after","user-agent"];e.exports=function(e){var t,r,s,i={};return e?(n.forEach(e.split("\n"),(function(e){if(s=e.indexOf(":"),t=n.trim(e.substr(0,s)).toLowerCase(),r=n.trim(e.substr(s+1)),t){if(i[t]&&o.indexOf(t)>=0)return;i[t]="set-cookie"===t?(i[t]?i[t]:[]).concat([r]):i[t]?i[t]+", "+r:r}})),i):i}},8713:e=>{"use strict";e.exports=function(e){return function(t){return e.apply(null,t)}}},4875:(e,t,r)=>{"use strict";var n=r(7288).version,o={};["object","boolean","number","function","string","symbol"].forEach((function(e,t){o[e]=function(r){return typeof r===e||"a"+(t<1?"n ":" ")+e}}));var s={};o.transitional=function(e,t,r){function o(e,t){return"[Axios v"+n+"] Transitional option '"+e+"'"+t+(r?". "+r:"")}return function(r,n,i){if(!1===e)throw new Error(o(n," has been removed"+(t?" in "+t:"")));return t&&!s[n]&&(s[n]=!0,console.warn(o(n," has been deprecated since v"+t+" and will be removed in the near future"))),!e||e(r,n,i)}},e.exports={assertOptions:function(e,t,r){if("object"!=typeof e)throw new TypeError("options must be an object");for(var n=Object.keys(e),o=n.length;o-- >0;){var s=n[o],i=t[s];if(i){var a=e[s],u=void 0===a||i(a,s,e);if(!0!==u)throw new TypeError("option "+s+" must be "+u)}else if(!0!==r)throw Error("Unknown option "+s)}},validators:o}},4867:(e,t,r)=>{"use strict";var n=r(1849),o=Object.prototype.toString;function s(e){return Array.isArray(e)}function i(e){return void 0===e}function a(e){return"[object ArrayBuffer]"===o.call(e)}function u(e){return null!==e&&"object"==typeof e}function c(e){if("[object Object]"!==o.call(e))return!1;var t=Object.getPrototypeOf(e);return null===t||t===Object.prototype}function l(e){return"[object Function]"===o.call(e)}function f(e,t){if(null!=e)if("object"!=typeof e&&(e=[e]),s(e))for(var r=0,n=e.length;r<n;r++)t.call(null,e[r],r,e);else for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&t.call(null,e[o],o,e)}e.exports={isArray:s,isArrayBuffer:a,isBuffer:function(e){return null!==e&&!i(e)&&null!==e.constructor&&!i(e.constructor)&&"function"==typeof e.constructor.isBuffer&&e.constructor.isBuffer(e)},isFormData:function(e){return"[object FormData]"===o.call(e)},isArrayBufferView:function(e){return"undefined"!=typeof ArrayBuffer&&ArrayBuffer.isView?ArrayBuffer.isView(e):e&&e.buffer&&a(e.buffer)},isString:function(e){return"string"==typeof e},isNumber:function(e){return"number"==typeof e},isObject:u,isPlainObject:c,isUndefined:i,isDate:function(e){return"[object Date]"===o.call(e)},isFile:function(e){return"[object File]"===o.call(e)},isBlob:function(e){return"[object Blob]"===o.call(e)},isFunction:l,isStream:function(e){return u(e)&&l(e.pipe)},isURLSearchParams:function(e){return"[object URLSearchParams]"===o.call(e)},isStandardBrowserEnv:function(){return("undefined"==typeof navigator||"ReactNative"!==navigator.product&&"NativeScript"!==navigator.product&&"NS"!==navigator.product)&&("undefined"!=typeof window&&"undefined"!=typeof document)},forEach:f,merge:function e(){var t={};function r(r,n){c(t[n])&&c(r)?t[n]=e(t[n],r):c(r)?t[n]=e({},r):s(r)?t[n]=r.slice():t[n]=r}for(var n=0,o=arguments.length;n<o;n++)f(arguments[n],r);return t},extend:function(e,t,r){return f(t,(function(t,o){e[o]=r&&"function"==typeof t?n(t,r):t})),e},trim:function(e){return e.trim?e.trim():e.replace(/^\s+|\s+$/g,"")},stripBOM:function(e){return 65279===e.charCodeAt(0)&&(e=e.slice(1)),e}}},1798:(e,t,r)=>{"use strict";r.r(t),r.d(t,{default:()=>i});var n=r(1369),o=r(5589);const s={layout:n.Z,props:{movimientos:Array,productos:Array,sucursales:Array,errors:Object},components:{Menu:o.Z},data:function(){return{isNew:!0,titulo:"Movimientos",textoVacio:"No existen Movimientos",fields:["producto",{label:"Origen",key:"soSucursal"},{label:"Destino",key:"sdSucursal"},"cantidad","nombre","apellido","observaciones",{label:"Fecha",key:"updated_at"}],itemRow:{},totalRows:1,currentPage:1,perPage:20}},methods:{getSucursal:function(e){var t="";for(var r in this.sucursales)e===this.sucursales[r].id&&(t=this.sucursales[r].nombre);return t},getProducto:function(e){var t="";for(var r in this.productos)e===this.productos[r].id&&(t=this.productos[r].formato+" ("+this.productos[r].dimension+")");return t}},mounted:function(){this.totalRows=this.movimientos.length}};const i=(0,r(1900).Z)(s,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"content-w"},[r("div",{staticClass:"content-box"},[r("Menu",{attrs:{active:2}}),e._v(" "),r("div",{staticClass:"tab-content"},[r("div",{staticClass:"m-b-20"},[r("div",{staticClass:"table-responsive"},[r("b-table",{attrs:{striped:"",hover:"",items:e.movimientos,fields:e.fields,"show-empty":"",small:"","current-page":e.currentPage,"per-page":e.perPage},scopedSlots:e._u([{key:"empty",fn:function(t){return[r("p",[e._v(e._s(e.textoVacio))])]}},{key:"cell(soSucursal)",fn:function(t){return[e._v("\n                            "+e._s(e.getSucursal(t.value))+"\n                        ")]}},{key:"cell(sdSucursal)",fn:function(t){return[e._v("\n                            "+e._s(e.getSucursal(t.value))+"\n                        ")]}},{key:"cell(producto)",fn:function(t){return[e._v("\n                            "+e._s(e.getProducto(t.value))+"\n                        ")]}}])},[e._v(" "),e._v(" "),e._v("\n                        +\n                        ")])],1),e._v(" "),r("b-col",[e.totalRows>e.perPage?r("b-pagination",{staticClass:"my-0",attrs:{"total-rows":e.totalRows,"per-page":e.perPage,align:"center"},model:{value:e.currentPage,callback:function(t){e.currentPage=t},expression:"currentPage"}}):e._e()],1)],1)])],1)])}),[],!1,null,null,null).exports},1369:(e,t,r)=>{"use strict";r.d(t,{Z:()=>d});r(9669);var n=r(6454);function o(e,t){var r="undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(!r){if(Array.isArray(e)||(r=function(e,t){if(!e)return;if("string"==typeof e)return s(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(e);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return s(e,t)}(e))||t&&e&&"number"==typeof e.length){r&&(e=r);var n=0,o=function(){};return{s:o,n:function(){return n>=e.length?{done:!0}:{done:!1,value:e[n++]}},e:function(e){throw e},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var i,a=!0,u=!1;return{s:function(){r=r.call(e)},n:function(){var e=r.next();return a=e.done,e},e:function(e){u=!0,i=e},f:function(){try{a||null==r.return||r.return()}finally{if(u)throw i}}}}function s(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}const i={name:"TheSidebar",components:{Link:n.rU},computed:{show:function(){return this.$store.state.sidebarShow},minimize:function(){return this.$store.state.sidebarMinimize}},data:function(){return{menu:[{titulo:"Agencia",submenu:[{Ordenes:[{label:"Nuevas Ordenes",url:"/ordenes",role:"all"},{label:"Buscar Ordenes",url:"/realizados",role:"all"}]},{Recibos:[{label:"Egresos",url:"/recibosEgreso",role:"vendor"},{label:"Ingresos",url:"/recibosIngreso",role:"vendor"}]},{label:"Caja Chica",url:"/cajaDebito",role:"vendor"},{Inventario:[{label:"Ingresos",url:"/inventario/ingreso",role:"vendor"},{label:"Egresos",url:"/inventario/egreso",role:"vendor"},{label:"Saldos",url:"/inventario/saldos",role:"vendor"}]},{reportes:[{label:"Registro Diario",url:"/reportes/placas",role:"vendor"},{label:"Rendicion Diaria",url:"/reportes/diario",role:"vendor"},{label:"Reporte cliente",url:"/reportes/cliente",role:"vendor"}]}]},{titulo:"Administracion",submenu:[{label:"Reportes",url:"/admin/reportes/placas",role:"admin"},{label:"Clientes",url:"/admin/clientes",role:"all"},{Productos:[{label:"Productos",url:"/admin/productos",role:"admin"},{url:"/admin/tipoProductos",label:"Tipo Productos",role:"admin"},{url:"/admin/stocks",label:"Stocks",role:"admin"}]},{Configuracion:[{label:"Sucursales",url:"/admin/sucursales",role:"admin"},{label:"Cajas",url:"/admin/cajas",role:"admin"},{label:"Usuarios",url:"/admin/users",role:"admin"}]}]}]}},methods:{getPermission:function(e){var t=!1;for(var r in this.$page.props.rolesP)if(r===e&&this.$page.props.rolesP[r].includes(this.$page.props.user.role)){t=!0;break}return t},getAllPermission:function(e){var t,r=!1,n=o(e);try{for(n.s();!(t=n.n()).done;){var s=t.value;for(var i in this.$page.props.rolesP)if(i===s.role&&this.$page.props.rolesP[i].includes(this.$page.props.user.role)){r=!0;break}}}catch(e){n.e(e)}finally{n.f()}return r},getUrl:function(){return window.location.pathname}}};var a=r(1900);const u=(0,a.Z)(i,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("CSidebar",{attrs:{fixed:"",minimize:e.minimize,show:e.show},on:{"update:show":function(t){return e.$store.commit("set",["sidebarShow",t])}}},[r("CSidebarBrand",{staticClass:"d-md-down-none",attrs:{to:"/"}},[e._v("\n        xCTP\n    ")]),e._v(" "),r("CSidebarNav",[e._l(e.menu,(function(t){return[r("CSidebarNavTitle",{staticClass:"text-uppercase"},[e._v("\n                "+e._s(t.titulo)+"\n            ")]),e._v(" "),e._l(t.submenu,(function(t,n){return[1===Object.values(t).length?[e.getAllPermission(Object.values(t)[0])?r("CSidebarNavDropdown",{staticClass:"text-capitalize",attrs:{name:Object.keys(t)[0]}},[e._l(Object.values(t)[0],(function(t,n){return[e.getPermission(t.role)?[r("li",{staticClass:"c-sidebar-nav-item"},[r("Link",{key:n,class:"c-sidebar-nav-link "+(e.getUrl()===t.url||e.getUrl()===t.url2?"c-active":""),attrs:{href:t.url}},[r("span",{staticClass:"text-capitalize"},[e._v(e._s(t.label))])])],1)]:e._e()]}))],2):e._e()]:[e.getPermission(t.role)?[r("li",{staticClass:"c-sidebar-nav-item"},[r("Link",{key:n,class:"c-sidebar-nav-link "+(e.getUrl()===t.url||e.getUrl()===t.url2?"c-active":""),attrs:{href:t.url}},[r("span",{staticClass:"text-capitalize"},[e._v(e._s(t.label))])])],1)]:e._e()]]}))]}))],2)],1)}),[],!1,null,null,null).exports;const c={name:"TheHeader",components:{Link:n.rU}};const l=(0,a.Z)(c,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("CHeader",{attrs:{fixed:"","with-subheader":"",light:""}},[r("CToggler",{staticClass:"ml-3 d-lg-none",attrs:{"in-header":""},on:{click:function(t){return e.$store.commit("toggleSidebarMobile")}}}),e._v(" "),r("CToggler",{staticClass:"ml-3 d-md-down-none",attrs:{"in-header":""},on:{click:function(t){return e.$store.commit("toggleSidebarDesktop")}}}),e._v(" "),r("CHeaderBrand",{staticClass:"mx-auto d-lg-none",attrs:{to:"/"}},[r("CIcon",{attrs:{name:"logo",height:"48",alt:"Logo"}})],1),e._v(" "),r("CHeaderNav",{staticClass:"d-md-down-none mr-auto"}),e._v(" "),r("CHeaderNav",{staticClass:"mr-4"},[r("CHeaderNavItem",{staticClass:"mx-2"},[r("Link",{staticClass:"c-header-nav-link",attrs:{href:"/logout",method:"post"}},[r("i",{staticClass:"mdi mdi-directions"}),e._v(" "),r("span",[e._v("Salir")])])],1)],1)],1)}),[],!1,null,null,null).exports,f={components:{Link:n.rU,TheSidebar:u,TheHeader:l},data:function(){return{}},methods:{},props:{title:String},created:function(){}};const d=(0,a.Z)(f,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"c-app"},[r("TheSidebar"),e._v(" "),r("CWrapper",[r("TheHeader"),e._v(" "),r("div",{staticClass:"c-body"},[r("main",{staticClass:"c-main"},[r("CContainer",{attrs:{fluid:""}},[r("transition",{attrs:{name:"fade",mode:"out-in"}},[e._t("default")],2)],1)],1)])],1)],1)}),[],!1,null,null,null).exports},4832:(e,t,r)=>{"use strict";r.d(t,{Z:()=>s});var n=r(6454);const o={props:{active:Number,links:Array},components:{Link:n.rU},methods:{isActive:function(e){return"nav-link"+(this.active===e?" active":"")}}};const s=(0,r(1900).Z)(o,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("ul",{staticClass:"nav nav-tabs"},[e._l(e.links,(function(t){return[r("li",{staticClass:"nav-item"},[r("Link",{class:e.isActive(t.active),attrs:{href:t.url}},[e._v(e._s(t.text))])],1)]}))],2)}),[],!1,null,null,null).exports},5589:(e,t,r)=>{"use strict";r.d(t,{Z:()=>s});var n=r(4832);const o={props:{active:Number},components:{TopMenu:n.Z},data:function(){return{links:[{active:1,url:"productos",text:"Productos"},{active:3,url:"tipoProductos",text:"Tipo Productos"},{active:0,url:"stocks",text:"Stocks"},{active:2,url:"movimientosStock",text:"Movimientos"}]}}};const s=(0,r(1900).Z)(o,(function(){var e=this,t=e.$createElement;return(e._self._c||t)("TopMenu",{attrs:{active:e.active,links:e.links}})}),[],!1,null,null,null).exports},1900:(e,t,r)=>{"use strict";function n(e,t,r,n,o,s,i,a){var u,c="function"==typeof e?e.options:e;if(t&&(c.render=t,c.staticRenderFns=r,c._compiled=!0),n&&(c.functional=!0),s&&(c._scopeId="data-v-"+s),i?(u=function(e){(e=e||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(e=__VUE_SSR_CONTEXT__),o&&o.call(this,e),e&&e._registeredComponents&&e._registeredComponents.add(i)},c._ssrRegister=u):o&&(u=a?function(){o.call(this,(c.functional?this.parent:this).$root.$options.shadowRoot)}:o),u)if(c.functional){c._injectStyles=u;var l=c.render;c.render=function(e,t){return u.call(t),l(e,t)}}else{var f=c.beforeCreate;c.beforeCreate=f?[].concat(f,u):[u]}return{exports:e,options:c}}r.d(t,{Z:()=>n})}}]);