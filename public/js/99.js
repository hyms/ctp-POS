(self.webpackChunk=self.webpackChunk||[]).push([[99,409,530],{9669:(e,t,r)=>{e.exports=r(1609)},5448:(e,t,r)=>{"use strict";var n=r(4867),o=r(6026),a=r(4372),s=r(5327),i=r(4097),l=r(4109),c=r(7985),u=r(5061),f=r(5655),d=r(4442);e.exports=function(e){return new Promise((function(t,r){var p,m=e.data,h=e.headers,v=e.responseType;function b(){e.cancelToken&&e.cancelToken.unsubscribe(p),e.signal&&e.signal.removeEventListener("abort",p)}n.isFormData(m)&&delete h["Content-Type"];var g=new XMLHttpRequest;if(e.auth){var y=e.auth.username||"",_=e.auth.password?unescape(encodeURIComponent(e.auth.password)):"";h.Authorization="Basic "+btoa(y+":"+_)}var x=i(e.baseURL,e.url);function C(){if(g){var n="getAllResponseHeaders"in g?l(g.getAllResponseHeaders()):null,a={data:v&&"text"!==v&&"json"!==v?g.response:g.responseText,status:g.status,statusText:g.statusText,headers:n,config:e,request:g};o((function(e){t(e),b()}),(function(e){r(e),b()}),a),g=null}}if(g.open(e.method.toUpperCase(),s(x,e.params,e.paramsSerializer),!0),g.timeout=e.timeout,"onloadend"in g?g.onloadend=C:g.onreadystatechange=function(){g&&4===g.readyState&&(0!==g.status||g.responseURL&&0===g.responseURL.indexOf("file:"))&&setTimeout(C)},g.onabort=function(){g&&(r(u("Request aborted",e,"ECONNABORTED",g)),g=null)},g.onerror=function(){r(u("Network Error",e,null,g)),g=null},g.ontimeout=function(){var t=e.timeout?"timeout of "+e.timeout+"ms exceeded":"timeout exceeded",n=e.transitional||f.transitional;e.timeoutErrorMessage&&(t=e.timeoutErrorMessage),r(u(t,e,n.clarifyTimeoutError?"ETIMEDOUT":"ECONNABORTED",g)),g=null},n.isStandardBrowserEnv()){var w=(e.withCredentials||c(x))&&e.xsrfCookieName?a.read(e.xsrfCookieName):void 0;w&&(h[e.xsrfHeaderName]=w)}"setRequestHeader"in g&&n.forEach(h,(function(e,t){void 0===m&&"content-type"===t.toLowerCase()?delete h[t]:g.setRequestHeader(t,e)})),n.isUndefined(e.withCredentials)||(g.withCredentials=!!e.withCredentials),v&&"json"!==v&&(g.responseType=e.responseType),"function"==typeof e.onDownloadProgress&&g.addEventListener("progress",e.onDownloadProgress),"function"==typeof e.onUploadProgress&&g.upload&&g.upload.addEventListener("progress",e.onUploadProgress),(e.cancelToken||e.signal)&&(p=function(e){g&&(r(!e||e&&e.type?new d("canceled"):e),g.abort(),g=null)},e.cancelToken&&e.cancelToken.subscribe(p),e.signal&&(e.signal.aborted?p():e.signal.addEventListener("abort",p))),m||(m=null),g.send(m)}))}},1609:(e,t,r)=>{"use strict";var n=r(4867),o=r(1849),a=r(321),s=r(7185);var i=function e(t){var r=new a(t),i=o(a.prototype.request,r);return n.extend(i,a.prototype,r),n.extend(i,r),i.create=function(r){return e(s(t,r))},i}(r(5655));i.Axios=a,i.Cancel=r(4442),i.CancelToken=r(4972),i.isCancel=r(6502),i.VERSION=r(7288).version,i.all=function(e){return Promise.all(e)},i.spread=r(8713),i.isAxiosError=r(6268),e.exports=i,e.exports.default=i},4442:e=>{"use strict";function t(e){this.message=e}t.prototype.toString=function(){return"Cancel"+(this.message?": "+this.message:"")},t.prototype.__CANCEL__=!0,e.exports=t},4972:(e,t,r)=>{"use strict";var n=r(4442);function o(e){if("function"!=typeof e)throw new TypeError("executor must be a function.");var t;this.promise=new Promise((function(e){t=e}));var r=this;this.promise.then((function(e){if(r._listeners){var t,n=r._listeners.length;for(t=0;t<n;t++)r._listeners[t](e);r._listeners=null}})),this.promise.then=function(e){var t,n=new Promise((function(e){r.subscribe(e),t=e})).then(e);return n.cancel=function(){r.unsubscribe(t)},n},e((function(e){r.reason||(r.reason=new n(e),t(r.reason))}))}o.prototype.throwIfRequested=function(){if(this.reason)throw this.reason},o.prototype.subscribe=function(e){this.reason?e(this.reason):this._listeners?this._listeners.push(e):this._listeners=[e]},o.prototype.unsubscribe=function(e){if(this._listeners){var t=this._listeners.indexOf(e);-1!==t&&this._listeners.splice(t,1)}},o.source=function(){var e;return{token:new o((function(t){e=t})),cancel:e}},e.exports=o},6502:e=>{"use strict";e.exports=function(e){return!(!e||!e.__CANCEL__)}},321:(e,t,r)=>{"use strict";var n=r(4867),o=r(5327),a=r(782),s=r(3572),i=r(7185),l=r(4875),c=l.validators;function u(e){this.defaults=e,this.interceptors={request:new a,response:new a}}u.prototype.request=function(e,t){"string"==typeof e?(t=t||{}).url=e:t=e||{},(t=i(this.defaults,t)).method?t.method=t.method.toLowerCase():this.defaults.method?t.method=this.defaults.method.toLowerCase():t.method="get";var r=t.transitional;void 0!==r&&l.assertOptions(r,{silentJSONParsing:c.transitional(c.boolean),forcedJSONParsing:c.transitional(c.boolean),clarifyTimeoutError:c.transitional(c.boolean)},!1);var n=[],o=!0;this.interceptors.request.forEach((function(e){"function"==typeof e.runWhen&&!1===e.runWhen(t)||(o=o&&e.synchronous,n.unshift(e.fulfilled,e.rejected))}));var a,u=[];if(this.interceptors.response.forEach((function(e){u.push(e.fulfilled,e.rejected)})),!o){var f=[s,void 0];for(Array.prototype.unshift.apply(f,n),f=f.concat(u),a=Promise.resolve(t);f.length;)a=a.then(f.shift(),f.shift());return a}for(var d=t;n.length;){var p=n.shift(),m=n.shift();try{d=p(d)}catch(e){m(e);break}}try{a=s(d)}catch(e){return Promise.reject(e)}for(;u.length;)a=a.then(u.shift(),u.shift());return a},u.prototype.getUri=function(e){return e=i(this.defaults,e),o(e.url,e.params,e.paramsSerializer).replace(/^\?/,"")},n.forEach(["delete","get","head","options"],(function(e){u.prototype[e]=function(t,r){return this.request(i(r||{},{method:e,url:t,data:(r||{}).data}))}})),n.forEach(["post","put","patch"],(function(e){u.prototype[e]=function(t,r,n){return this.request(i(n||{},{method:e,url:t,data:r}))}})),e.exports=u},782:(e,t,r)=>{"use strict";var n=r(4867);function o(){this.handlers=[]}o.prototype.use=function(e,t,r){return this.handlers.push({fulfilled:e,rejected:t,synchronous:!!r&&r.synchronous,runWhen:r?r.runWhen:null}),this.handlers.length-1},o.prototype.eject=function(e){this.handlers[e]&&(this.handlers[e]=null)},o.prototype.forEach=function(e){n.forEach(this.handlers,(function(t){null!==t&&e(t)}))},e.exports=o},4097:(e,t,r)=>{"use strict";var n=r(1793),o=r(7303);e.exports=function(e,t){return e&&!n(t)?o(e,t):t}},5061:(e,t,r)=>{"use strict";var n=r(481);e.exports=function(e,t,r,o,a){var s=new Error(e);return n(s,t,r,o,a)}},3572:(e,t,r)=>{"use strict";var n=r(4867),o=r(8527),a=r(6502),s=r(5655),i=r(4442);function l(e){if(e.cancelToken&&e.cancelToken.throwIfRequested(),e.signal&&e.signal.aborted)throw new i("canceled")}e.exports=function(e){return l(e),e.headers=e.headers||{},e.data=o.call(e,e.data,e.headers,e.transformRequest),e.headers=n.merge(e.headers.common||{},e.headers[e.method]||{},e.headers),n.forEach(["delete","get","head","post","put","patch","common"],(function(t){delete e.headers[t]})),(e.adapter||s.adapter)(e).then((function(t){return l(e),t.data=o.call(e,t.data,t.headers,e.transformResponse),t}),(function(t){return a(t)||(l(e),t&&t.response&&(t.response.data=o.call(e,t.response.data,t.response.headers,e.transformResponse))),Promise.reject(t)}))}},481:e=>{"use strict";e.exports=function(e,t,r,n,o){return e.config=t,r&&(e.code=r),e.request=n,e.response=o,e.isAxiosError=!0,e.toJSON=function(){return{message:this.message,name:this.name,description:this.description,number:this.number,fileName:this.fileName,lineNumber:this.lineNumber,columnNumber:this.columnNumber,stack:this.stack,config:this.config,code:this.code,status:this.response&&this.response.status?this.response.status:null}},e}},7185:(e,t,r)=>{"use strict";var n=r(4867);e.exports=function(e,t){t=t||{};var r={};function o(e,t){return n.isPlainObject(e)&&n.isPlainObject(t)?n.merge(e,t):n.isPlainObject(t)?n.merge({},t):n.isArray(t)?t.slice():t}function a(r){return n.isUndefined(t[r])?n.isUndefined(e[r])?void 0:o(void 0,e[r]):o(e[r],t[r])}function s(e){if(!n.isUndefined(t[e]))return o(void 0,t[e])}function i(r){return n.isUndefined(t[r])?n.isUndefined(e[r])?void 0:o(void 0,e[r]):o(void 0,t[r])}function l(r){return r in t?o(e[r],t[r]):r in e?o(void 0,e[r]):void 0}var c={url:s,method:s,data:s,baseURL:i,transformRequest:i,transformResponse:i,paramsSerializer:i,timeout:i,timeoutMessage:i,withCredentials:i,adapter:i,responseType:i,xsrfCookieName:i,xsrfHeaderName:i,onUploadProgress:i,onDownloadProgress:i,decompress:i,maxContentLength:i,maxBodyLength:i,transport:i,httpAgent:i,httpsAgent:i,cancelToken:i,socketPath:i,responseEncoding:i,validateStatus:l};return n.forEach(Object.keys(e).concat(Object.keys(t)),(function(e){var t=c[e]||a,o=t(e);n.isUndefined(o)&&t!==l||(r[e]=o)})),r}},6026:(e,t,r)=>{"use strict";var n=r(5061);e.exports=function(e,t,r){var o=r.config.validateStatus;r.status&&o&&!o(r.status)?t(n("Request failed with status code "+r.status,r.config,null,r.request,r)):e(r)}},8527:(e,t,r)=>{"use strict";var n=r(4867),o=r(5655);e.exports=function(e,t,r){var a=this||o;return n.forEach(r,(function(r){e=r.call(a,e,t)})),e}},5655:(e,t,r)=>{"use strict";var n=r(4155),o=r(4867),a=r(6016),s=r(481),i={"Content-Type":"application/x-www-form-urlencoded"};function l(e,t){!o.isUndefined(e)&&o.isUndefined(e["Content-Type"])&&(e["Content-Type"]=t)}var c,u={transitional:{silentJSONParsing:!0,forcedJSONParsing:!0,clarifyTimeoutError:!1},adapter:(("undefined"!=typeof XMLHttpRequest||void 0!==n&&"[object process]"===Object.prototype.toString.call(n))&&(c=r(5448)),c),transformRequest:[function(e,t){return a(t,"Accept"),a(t,"Content-Type"),o.isFormData(e)||o.isArrayBuffer(e)||o.isBuffer(e)||o.isStream(e)||o.isFile(e)||o.isBlob(e)?e:o.isArrayBufferView(e)?e.buffer:o.isURLSearchParams(e)?(l(t,"application/x-www-form-urlencoded;charset=utf-8"),e.toString()):o.isObject(e)||t&&"application/json"===t["Content-Type"]?(l(t,"application/json"),function(e,t,r){if(o.isString(e))try{return(t||JSON.parse)(e),o.trim(e)}catch(e){if("SyntaxError"!==e.name)throw e}return(r||JSON.stringify)(e)}(e)):e}],transformResponse:[function(e){var t=this.transitional||u.transitional,r=t&&t.silentJSONParsing,n=t&&t.forcedJSONParsing,a=!r&&"json"===this.responseType;if(a||n&&o.isString(e)&&e.length)try{return JSON.parse(e)}catch(e){if(a){if("SyntaxError"===e.name)throw s(e,this,"E_JSON_PARSE");throw e}}return e}],timeout:0,xsrfCookieName:"XSRF-TOKEN",xsrfHeaderName:"X-XSRF-TOKEN",maxContentLength:-1,maxBodyLength:-1,validateStatus:function(e){return e>=200&&e<300},headers:{common:{Accept:"application/json, text/plain, */*"}}};o.forEach(["delete","get","head"],(function(e){u.headers[e]={}})),o.forEach(["post","put","patch"],(function(e){u.headers[e]=o.merge(i)})),e.exports=u},7288:e=>{e.exports={version:"0.26.0"}},1849:e=>{"use strict";e.exports=function(e,t){return function(){for(var r=new Array(arguments.length),n=0;n<r.length;n++)r[n]=arguments[n];return e.apply(t,r)}}},5327:(e,t,r)=>{"use strict";var n=r(4867);function o(e){return encodeURIComponent(e).replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"+").replace(/%5B/gi,"[").replace(/%5D/gi,"]")}e.exports=function(e,t,r){if(!t)return e;var a;if(r)a=r(t);else if(n.isURLSearchParams(t))a=t.toString();else{var s=[];n.forEach(t,(function(e,t){null!=e&&(n.isArray(e)?t+="[]":e=[e],n.forEach(e,(function(e){n.isDate(e)?e=e.toISOString():n.isObject(e)&&(e=JSON.stringify(e)),s.push(o(t)+"="+o(e))})))})),a=s.join("&")}if(a){var i=e.indexOf("#");-1!==i&&(e=e.slice(0,i)),e+=(-1===e.indexOf("?")?"?":"&")+a}return e}},7303:e=>{"use strict";e.exports=function(e,t){return t?e.replace(/\/+$/,"")+"/"+t.replace(/^\/+/,""):e}},4372:(e,t,r)=>{"use strict";var n=r(4867);e.exports=n.isStandardBrowserEnv()?{write:function(e,t,r,o,a,s){var i=[];i.push(e+"="+encodeURIComponent(t)),n.isNumber(r)&&i.push("expires="+new Date(r).toGMTString()),n.isString(o)&&i.push("path="+o),n.isString(a)&&i.push("domain="+a),!0===s&&i.push("secure"),document.cookie=i.join("; ")},read:function(e){var t=document.cookie.match(new RegExp("(^|;\\s*)("+e+")=([^;]*)"));return t?decodeURIComponent(t[3]):null},remove:function(e){this.write(e,"",Date.now()-864e5)}}:{write:function(){},read:function(){return null},remove:function(){}}},1793:e=>{"use strict";e.exports=function(e){return/^([a-z][a-z\d+\-.]*:)?\/\//i.test(e)}},6268:(e,t,r)=>{"use strict";var n=r(4867);e.exports=function(e){return n.isObject(e)&&!0===e.isAxiosError}},7985:(e,t,r)=>{"use strict";var n=r(4867);e.exports=n.isStandardBrowserEnv()?function(){var e,t=/(msie|trident)/i.test(navigator.userAgent),r=document.createElement("a");function o(e){var n=e;return t&&(r.setAttribute("href",n),n=r.href),r.setAttribute("href",n),{href:r.href,protocol:r.protocol?r.protocol.replace(/:$/,""):"",host:r.host,search:r.search?r.search.replace(/^\?/,""):"",hash:r.hash?r.hash.replace(/^#/,""):"",hostname:r.hostname,port:r.port,pathname:"/"===r.pathname.charAt(0)?r.pathname:"/"+r.pathname}}return e=o(window.location.href),function(t){var r=n.isString(t)?o(t):t;return r.protocol===e.protocol&&r.host===e.host}}():function(){return!0}},6016:(e,t,r)=>{"use strict";var n=r(4867);e.exports=function(e,t){n.forEach(e,(function(r,n){n!==t&&n.toUpperCase()===t.toUpperCase()&&(e[t]=r,delete e[n])}))}},4109:(e,t,r)=>{"use strict";var n=r(4867),o=["age","authorization","content-length","content-type","etag","expires","from","host","if-modified-since","if-unmodified-since","last-modified","location","max-forwards","proxy-authorization","referer","retry-after","user-agent"];e.exports=function(e){var t,r,a,s={};return e?(n.forEach(e.split("\n"),(function(e){if(a=e.indexOf(":"),t=n.trim(e.substr(0,a)).toLowerCase(),r=n.trim(e.substr(a+1)),t){if(s[t]&&o.indexOf(t)>=0)return;s[t]="set-cookie"===t?(s[t]?s[t]:[]).concat([r]):s[t]?s[t]+", "+r:r}})),s):s}},8713:e=>{"use strict";e.exports=function(e){return function(t){return e.apply(null,t)}}},4875:(e,t,r)=>{"use strict";var n=r(7288).version,o={};["object","boolean","number","function","string","symbol"].forEach((function(e,t){o[e]=function(r){return typeof r===e||"a"+(t<1?"n ":" ")+e}}));var a={};o.transitional=function(e,t,r){function o(e,t){return"[Axios v"+n+"] Transitional option '"+e+"'"+t+(r?". "+r:"")}return function(r,n,s){if(!1===e)throw new Error(o(n," has been removed"+(t?" in "+t:"")));return t&&!a[n]&&(a[n]=!0,console.warn(o(n," has been deprecated since v"+t+" and will be removed in the near future"))),!e||e(r,n,s)}},e.exports={assertOptions:function(e,t,r){if("object"!=typeof e)throw new TypeError("options must be an object");for(var n=Object.keys(e),o=n.length;o-- >0;){var a=n[o],s=t[a];if(s){var i=e[a],l=void 0===i||s(i,a,e);if(!0!==l)throw new TypeError("option "+a+" must be "+l)}else if(!0!==r)throw Error("Unknown option "+a)}},validators:o}},4867:(e,t,r)=>{"use strict";var n=r(1849),o=Object.prototype.toString;function a(e){return Array.isArray(e)}function s(e){return void 0===e}function i(e){return"[object ArrayBuffer]"===o.call(e)}function l(e){return null!==e&&"object"==typeof e}function c(e){if("[object Object]"!==o.call(e))return!1;var t=Object.getPrototypeOf(e);return null===t||t===Object.prototype}function u(e){return"[object Function]"===o.call(e)}function f(e,t){if(null!=e)if("object"!=typeof e&&(e=[e]),a(e))for(var r=0,n=e.length;r<n;r++)t.call(null,e[r],r,e);else for(var o in e)Object.prototype.hasOwnProperty.call(e,o)&&t.call(null,e[o],o,e)}e.exports={isArray:a,isArrayBuffer:i,isBuffer:function(e){return null!==e&&!s(e)&&null!==e.constructor&&!s(e.constructor)&&"function"==typeof e.constructor.isBuffer&&e.constructor.isBuffer(e)},isFormData:function(e){return"[object FormData]"===o.call(e)},isArrayBufferView:function(e){return"undefined"!=typeof ArrayBuffer&&ArrayBuffer.isView?ArrayBuffer.isView(e):e&&e.buffer&&i(e.buffer)},isString:function(e){return"string"==typeof e},isNumber:function(e){return"number"==typeof e},isObject:l,isPlainObject:c,isUndefined:s,isDate:function(e){return"[object Date]"===o.call(e)},isFile:function(e){return"[object File]"===o.call(e)},isBlob:function(e){return"[object Blob]"===o.call(e)},isFunction:u,isStream:function(e){return l(e)&&u(e.pipe)},isURLSearchParams:function(e){return"[object URLSearchParams]"===o.call(e)},isStandardBrowserEnv:function(){return("undefined"==typeof navigator||"ReactNative"!==navigator.product&&"NativeScript"!==navigator.product&&"NS"!==navigator.product)&&("undefined"!=typeof window&&"undefined"!=typeof document)},forEach:f,merge:function e(){var t={};function r(r,n){c(t[n])&&c(r)?t[n]=e(t[n],r):c(r)?t[n]=e({},r):a(r)?t[n]=r.slice():t[n]=r}for(var n=0,o=arguments.length;n<o;n++)f(arguments[n],r);return t},extend:function(e,t,r){return f(t,(function(t,o){e[o]=r&&"function"==typeof t?n(t,r):t})),e},trim:function(e){return e.trim?e.trim():e.replace(/^\s+|\s+$/g,"")},stripBOM:function(e){return 65279===e.charCodeAt(0)&&(e=e.slice(1)),e}}},9409:(e,t,r)=>{"use strict";r.r(t),r.d(t,{default:()=>u});var n=r(9669),o=r.n(n),a=r(6118);function s(e,t){var r="undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(!r){if(Array.isArray(e)||(r=function(e,t){if(!e)return;if("string"==typeof e)return i(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(e);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return i(e,t)}(e))||t&&e&&"number"==typeof e.length){r&&(e=r);var n=0,o=function(){};return{s:o,n:function(){return n>=e.length?{done:!0}:{done:!1,value:e[n++]}},e:function(e){throw e},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var a,s=!0,l=!1;return{s:function(){r=r.call(e)},n:function(){var e=r.next();return s=e.done,e},e:function(e){l=!0,a=e},f:function(){try{s||null==r.return||r.return()}finally{if(l)throw a}}}}function i(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}const l={data:function(){return{titulo:"Orden",total:0,monto:0,sending:!1}},props:{productos:Array,item:Object,id:String,isVenta:Boolean},components:{LoadingButton:a.Z},methods:{getProduct:function(e){var t,r={},n=s(this.productos);try{for(n.s();!(t=n.n()).done;){var o=t.value;if(o.id==e){r=o;break}}}catch(e){n.e(e)}finally{n.f()}return r?r.formato+" ("+r.dimension+")":""},guardar:function(e){var t=this;this.sending=!0,this.reajusteOrden();var r=new FormData;r.append("item",JSON.stringify(this.item)),this.monto&&r.append("monto",this.monto),o().post(e,r,{headers:{"Content-Type":"multipart/form-data"}}).then((function(e){var r=e.data;if(0==r.status)t.$bvModal.hide(t.id),t.$inertia.get(r.path);else for(var n in t.form)n in r.errors?(t.form[n].state=!1,t.form[n].stateText=r.errors[n][0]):(t.form[n].state=!0,t.form[n].stateText="")})).catch((function(e){t.errors=e,console.log(e)})).finally((function(){t.sending=!1}))},guardarVenta:function(){this.guardar("/ordenVenta")},guardarDeuda:function(){this.guardar("/ordenDeuda")},getTotal:function(e){var t=0;if(e){var r,n=s(e);try{for(n.s();!(r=n.n()).done;){var o=r.value;o&&(t+=o.costo*o.cantidad)}}catch(e){n.e(e)}finally{n.f()}this.total=t}return t},getCambio:function(){return this.item.montoVenta-this.total},getSaldo:function(){return this.total-this.item.montoVenta-this.monto},getObservaciones:function(e){return e?e.replace(/\n/g,"<br/>"):""},reajusteOrden:function(){this.item.montoVenta>this.total&&(this.item.montoVenta=this.total)},getUrlPrint:function(e){return this.isVenta?"/ordenPdfV/"+e:"/ordenPdf/"+e}}},c=l;const u=(0,r(1900).Z)(c,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("b-modal",{attrs:{id:e.id,title:e.titulo+" "+(e.item.codigoServicio?e.item.codigoServicio:e.item.correlativo)},scopedSlots:e._u([{key:"modal-footer",fn:function(t){t.ok;var n=t.cancel;return[r("b-button",{attrs:{size:"sm",variant:"danger"},on:{click:function(e){return n()}}},[e._v("\n            Cerrar\n        ")]),e._v(" "),r("a",{staticClass:"btn btn-outline-primary btn-sm",attrs:{href:e.getUrlPrint(e.item.id),target:"_blank"}},[e._v("Imprimir")]),e._v(" "),e.isVenta&&[1,5].includes(e.item.estado)?r("loading-button",{attrs:{loading:e.sending,variant:"primary",size:"sm",text:"Guardar",textLoad:"Guardando"},nativeOn:{click:function(t){return e.guardarVenta()}}},[e._v("Guardar\n        ")]):e._e(),e._v(" "),e.isVenta&&2==e.item.estado?r("loading-button",{attrs:{loading:e.sending,variant:"primary",size:"sm",text:"Pagar",textLoad:"Pagando"},nativeOn:{click:function(t){return e.guardarDeuda()}}},[e._v("Pagar\n        ")]):e._e()]}}])},[r("div",[r("strong",[e._v("Cliente:")]),e._v(" "+e._s(e.item.responsable))]),e._v(" "),r("div",[r("strong",[e._v("Telefono:")]),e._v(" "+e._s(e.item.telefono))]),e._v(" "),r("br"),e._v(" "),r("div",{staticClass:"table-responsive"},[r("table",{staticClass:"table"},[r("thead",[r("tr",[r("th",{attrs:{scope:"col"}},[e._v("#")]),e._v(" "),r("th",{attrs:{scope:"col"}},[e._v("Productos")]),e._v(" "),r("th",{attrs:{scope:"col"}},[e._v("Cant.")]),e._v(" "),e.isVenta?r("th",{attrs:{scope:"col"}},[e._v("Precio")]):e._e(),e._v(" "),e.isVenta?r("th",{attrs:{scope:"col"}},[e._v("Total")]):e._e()])]),e._v(" "),r("tbody",e._l(e.item.detallesOrden,(function(t,n){return r("tr",[r("td",[e._v(e._s(n+1))]),e._v(" "),r("td",[e._v(e._s(e.getProduct(t.stock)))]),e._v(" "),r("td",[e._v(e._s(t.cantidad))]),e._v(" "),e.isVenta?r("td",[[1,5].includes(e.item.estado)?r("b-input",{attrs:{type:"text",size:"sm"},model:{value:t.costo,callback:function(r){e.$set(t,"costo",r)},expression:"itemOrden.costo"}}):[e._v(e._s(t.costo))]],2):e._e(),e._v(" "),e.isVenta?r("td",[e._v(e._s(t.costo*t.cantidad))]):e._e()])})),0),e._v(" "),e.isVenta?r("tfoot",[r("tr",[r("td",{staticClass:"text-right",attrs:{colspan:"4"}},[r("strong",[e._v("Total")])]),e._v(" "),r("td",[e._v(e._s(e.getTotal(e.item.detallesOrden)))])]),e._v(" "),[1,5].includes(e.item.estado)?[r("tr",[r("td",{staticClass:"text-right",attrs:{colspan:"4"}},[r("strong",[e._v("Cancelado")])]),e._v(" "),r("td",[[1,5].includes(e.item.estado)?r("b-input",{attrs:{type:"text",size:"sm"},model:{value:e.item.montoVenta,callback:function(t){e.$set(e.item,"montoVenta",t)},expression:"item.montoVenta"}}):[e._v(e._s(e.item.montoVenta))]],2)]),e._v(" "),r("tr",[r("td",{staticClass:"text-right",attrs:{colspan:"4"}},[r("strong",[e._v("Cambio")])]),e._v(" "),r("td",[e._v(e._s(e.getCambio()))])])]:e._e()],2):e._e()])]),e._v(" "),2==e.item.estado?r("div",[r("form",[r("b-row",[r("b-col",[r("b-form-group",{attrs:{label:"Cancelado","label-for":"cancelado"}},[r("b-input",{attrs:{type:"text",placeholder:"Cancelado",id:"cancelado",name:"cancelado",disabled:""},model:{value:e.item.montoVenta,callback:function(t){e.$set(e.item,"montoVenta",t)},expression:"item.montoVenta"}})],1)],1),e._v(" "),r("b-col",[r("b-form-group",{attrs:{label:"A Cuenta","label-for":"cuenta"}},[r("b-input",{attrs:{type:"text",placeholder:"Cuenta",id:"cuenta",name:"cuenta"},model:{value:e.monto,callback:function(t){e.monto=t},expression:"monto"}})],1)],1),e._v(" "),r("b-col",[r("b-form-group",{attrs:{label:"Saldo","label-for":"saldo"}},[r("b-input",{attrs:{type:"text",placeholder:"Saldo",value:e.getSaldo(),id:"saldo",name:"saldo",disabled:""}})],1)],1)],1)],1)]):e._e(),e._v(" "),r("div",[r("p",[r("strong",[e._v("Observaciones:")]),r("br"),e._v(" "),r("span",{domProps:{innerHTML:e._s(e.getObservaciones(e.item.observaciones))}})])])])}),[],!1,null,null,null).exports},99:(e,t,r)=>{"use strict";r.r(t),r.d(t,{default:()=>l});var n=r(1369),o=r(7530),a=r(9409);const s={layout:n.Z,name:"xclientes",components:{Menu:o.default,itemOrden:a.default},props:{admin:Boolean,sucursales:Object,clientes:Object,request:Object,data:Object,productosAll:Array},data:function(){return{form:{fechaI:{label:"Fecha Inicio",value:"",type:"date",state:null,stateText:null},fechaF:{label:"Fecha Final",value:"",type:"date",state:null,stateText:null},sucursal:{label:"Sucursal",value:"",type:"select",state:null,stateText:null},cliente:{label:"Cliente",value:"",type:"select",state:null,stateText:null}},itemRow:{}}},methods:{enviar:function(){var e={};e.fechaI=this.form.fechaI.value,e.cliente=this.form.cliente.value,e.fechaF=this.form.fechaF.value,this.admin&&(e.sucursal=this.form.sucursal.value),this.$inertia.get(window.location.pathname,e)},loadModal:function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:null;this.tipoProductoFiltro=e,this.itemRow={},this.itemRow=t.item}},mounted:function(){for(var e in this.form)void 0!==this.request[e]&&""!==this.request[e]&&(this.form[e].value=this.request[e])}},i=s;const l=(0,r(1900).Z)(i,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"content-w"},[r("div",{staticClass:"content-box"},[e.admin?r("Menu",{attrs:{active:6}}):r("div",{staticClass:"row"},[r("div",{staticClass:"col-sm-12"},[r("h4",{staticClass:"header-title m-t-0 m-b-20"},[e._v("Reporte por clientes")])])]),e._v(" "),r("item-orden",{attrs:{id:"itemModal",isVenta:!1,item:e.itemRow,productos:e.productosAll}}),e._v(" "),r("b-card",[r("b-form",{on:{submit:function(t){return t.preventDefault(),e.enviar.apply(null,arguments)}}},[r("b-row",[e.admin&&Object.keys(e.sucursales).length>0?r("b-col",{attrs:{md:"4",sm:"6"}},[r("b-form-group",{attrs:{label:e.form.sucursal.label,"label-for":"sucursal",state:e.form.sucursal.state}},[r("b-form-select",{attrs:{placeholder:e.form.sucursal.label,options:e.sucursales,id:"sucursal",state:e.form.sucursal.state},scopedSlots:e._u([{key:"first",fn:function(){return[r("b-form-select-option",{attrs:{value:""}},[e._v("-- Seleccione una sucursal --\n                                    ")])]},proxy:!0}],null,!1,3928538531),model:{value:e.form.sucursal.value,callback:function(t){e.$set(e.form.sucursal,"value",t)},expression:"form.sucursal.value"}})],1)],1):e._e(),e._v(" "),r("b-col",{attrs:{md:"4",sm:"6"}},[r("b-form-group",{attrs:{label:e.form.fechaI.label,"label-for":"fechaI",state:e.form.fechaI.state}},[r("b-input",{attrs:{type:e.form.fechaI.type,placeholder:e.form.fechaI.label,id:"fechaI",state:e.form.fechaI.state},model:{value:e.form.fechaI.value,callback:function(t){e.$set(e.form.fechaI,"value",t)},expression:"form.fechaI.value"}})],1)],1),e._v(" "),r("b-col",{attrs:{md:"4",sm:"6"}},[r("b-form-group",{attrs:{label:e.form.fechaF.label,"label-for":"fechaF",state:e.form.fechaF.state}},[r("b-input",{attrs:{type:e.form.fechaF.type,placeholder:e.form.fechaF.label,id:"fechaF",state:e.form.fechaF.state},model:{value:e.form.fechaF.value,callback:function(t){e.$set(e.form.fechaF,"value",t)},expression:"form.fechaF.value"}})],1)],1),e._v(" "),r("b-col",{attrs:{md:"4",sm:"6"}},[r("b-form-group",{attrs:{label:e.form.cliente.label,"label-for":"cliente",state:e.form.cliente.state}},[r("b-form-select",{attrs:{placeholder:e.form.cliente.label,options:e.clientes,id:"sucursal",state:e.form.cliente.state},scopedSlots:e._u([{key:"first",fn:function(){return[r("b-form-select-option",{attrs:{value:""}},[e._v("-- Seleccione un cliente --\n                                    ")])]},proxy:!0}]),model:{value:e.form.cliente.value,callback:function(t){e.$set(e.form.cliente,"value",t)},expression:"form.cliente.value"}})],1)],1)],1),e._v(" "),r("b-row",[r("b-col",[r("b-button",{attrs:{type:"submit",size:"sm",variant:"primary"}},[e._v("Buscar")])],1)],1)],1)],1),e._v(" "),r("b-card",{staticClass:"mb-1"},[r("b-card-body",{staticClass:"table-responsive"},[r("b-table",{attrs:{striped:"",hover:"",items:e.data.table,fields:e.data.fields,"show-empty":"",small:""},scopedSlots:e._u([{key:"cell(created_at)",fn:function(t){return[e._v("\n                        "+e._s(e._f("moment")(t.value,"DD/MM/YYYY HH:mm"))+"\n                    ")]}},{key:"empty",fn:function(t){return[r("p",[e._v("No existen Datos")])]}},{key:"cell(Acciones)",fn:function(t){return[r("div",{staticClass:"row-actions"},[r("b-button",{directives:[{name:"b-modal",rawName:"v-b-modal",value:"itemModal",expression:"'itemModal'"}],attrs:{variant:"primary",size:"sm"},on:{click:function(r){return e.loadModal(t.item.tipoOrden,t)}}},[e._v("\n                                Ver\n                            ")])],1)]}}])})],1)],1)],1)])}),[],!1,null,null,null).exports},7530:(e,t,r)=>{"use strict";r.r(t),r.d(t,{default:()=>a});var n=r(4832);const o={props:{active:Number},components:{TopMenu:n.Z},data:function(){return{links:[{active:0,url:"resumen",text:"Resumen"},{active:1,url:"placas",text:"Placas"},{active:2,url:"arqueos",text:"Arqueos"},{active:4,url:"mora",text:"Mora"},{active:6,text:"Rendicion Diaria",url:"rendicion"}]}}};const a=(0,r(1900).Z)(o,(function(){var e=this,t=e.$createElement;return(e._self._c||t)("TopMenu",{attrs:{active:e.active,links:e.links}})}),[],!1,null,null,null).exports},1369:(e,t,r)=>{"use strict";r.d(t,{Z:()=>d});r(9669);var n=r(6454);function o(e,t){var r="undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(!r){if(Array.isArray(e)||(r=function(e,t){if(!e)return;if("string"==typeof e)return a(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(e);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return a(e,t)}(e))||t&&e&&"number"==typeof e.length){r&&(e=r);var n=0,o=function(){};return{s:o,n:function(){return n>=e.length?{done:!0}:{done:!1,value:e[n++]}},e:function(e){throw e},f:o}}throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}var s,i=!0,l=!1;return{s:function(){r=r.call(e)},n:function(){var e=r.next();return i=e.done,e},e:function(e){l=!0,s=e},f:function(){try{i||null==r.return||r.return()}finally{if(l)throw s}}}}function a(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}const s={name:"TheSidebar",components:{Link:n.rU},computed:{show:function(){return this.$store.state.sidebarShow},minimize:function(){return this.$store.state.sidebarMinimize}},data:function(){return{menu:[{titulo:"Agencia",submenu:[{Ordenes:[{label:"Nuevas Ordenes",url:"/ordenes",role:"all"},{label:"Buscar Ordenes",url:"/realizados",role:"all"}]},{Recibos:[{label:"Egresos",url:"/recibosEgreso",role:"vendor"},{label:"Ingresos",url:"/recibosIngreso",role:"vendor"}]},{label:"Caja Chica",url:"/cajaDebito",role:"vendor"},{Inventario:[{label:"Ingresos",url:"/inventario/ingreso",role:"vendor"},{label:"Egresos",url:"/inventario/egreso",role:"vendor"},{label:"Saldos",url:"/inventario/saldos",role:"vendor"}]},{reportes:[{label:"Registro Diario",url:"/reportes/placas",role:"vendor"},{label:"Rendicion Diaria",url:"/reportes/diario",role:"vendor"},{label:"Reporte cliente",url:"/reportes/cliente",role:"vendor"}]}]},{titulo:"Administracion",submenu:[{label:"Reportes",url:"/admin/reportes/placas",role:"admin"},{label:"Clientes",url:"/admin/clientes",role:"all"},{Productos:[{label:"Productos",url:"/admin/productos",role:"admin"},{url:"/admin/tipoProductos",label:"Tipo Productos",role:"admin"},{url:"/admin/stocks",label:"Stocks",role:"admin"}]},{Configuracion:[{label:"Sucursales",url:"/admin/sucursales",role:"admin"},{label:"Cajas",url:"/admin/cajas",role:"admin"},{label:"Usuarios",url:"/admin/users",role:"admin"}]}]}]}},methods:{getPermission:function(e){var t=!1;for(var r in this.$page.props.rolesP)if(r===e&&this.$page.props.rolesP[r].includes(this.$page.props.user.role)){t=!0;break}return t},getAllPermission:function(e){var t,r=!1,n=o(e);try{for(n.s();!(t=n.n()).done;){var a=t.value;for(var s in this.$page.props.rolesP)if(s===a.role&&this.$page.props.rolesP[s].includes(this.$page.props.user.role)){r=!0;break}}}catch(e){n.e(e)}finally{n.f()}return r},getUrl:function(){return window.location.pathname}}};var i=r(1900);const l=(0,i.Z)(s,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("CSidebar",{attrs:{fixed:"",minimize:e.minimize,show:e.show},on:{"update:show":function(t){return e.$store.commit("set",["sidebarShow",t])}}},[r("CSidebarBrand",{staticClass:"d-md-down-none",attrs:{to:"/"}},[e._v("\n        xCTP\n    ")]),e._v(" "),r("CSidebarNav",[e._l(e.menu,(function(t){return[r("CSidebarNavTitle",{staticClass:"text-uppercase"},[e._v("\n                "+e._s(t.titulo)+"\n            ")]),e._v(" "),e._l(t.submenu,(function(t,n){return[1===Object.values(t).length?[e.getAllPermission(Object.values(t)[0])?r("CSidebarNavDropdown",{staticClass:"text-capitalize",attrs:{name:Object.keys(t)[0]}},[e._l(Object.values(t)[0],(function(t,n){return[e.getPermission(t.role)?[r("li",{staticClass:"c-sidebar-nav-item"},[r("Link",{key:n,class:"c-sidebar-nav-link "+(e.getUrl()===t.url||e.getUrl()===t.url2?"c-active":""),attrs:{href:t.url}},[r("span",{staticClass:"text-capitalize"},[e._v(e._s(t.label))])])],1)]:e._e()]}))],2):e._e()]:[e.getPermission(t.role)?[r("li",{staticClass:"c-sidebar-nav-item"},[r("Link",{key:n,class:"c-sidebar-nav-link "+(e.getUrl()===t.url||e.getUrl()===t.url2?"c-active":""),attrs:{href:t.url}},[r("span",{staticClass:"text-capitalize"},[e._v(e._s(t.label))])])],1)]:e._e()]]}))]}))],2)],1)}),[],!1,null,null,null).exports;const c={name:"TheHeader",components:{Link:n.rU}};const u=(0,i.Z)(c,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("CHeader",{attrs:{fixed:"","with-subheader":"",light:""}},[r("CToggler",{staticClass:"ml-3 d-lg-none",attrs:{"in-header":""},on:{click:function(t){return e.$store.commit("toggleSidebarMobile")}}}),e._v(" "),r("CToggler",{staticClass:"ml-3 d-md-down-none",attrs:{"in-header":""},on:{click:function(t){return e.$store.commit("toggleSidebarDesktop")}}}),e._v(" "),r("CHeaderBrand",{staticClass:"mx-auto d-lg-none",attrs:{to:"/"}},[r("CIcon",{attrs:{name:"logo",height:"48",alt:"Logo"}})],1),e._v(" "),r("CHeaderNav",{staticClass:"d-md-down-none mr-auto"}),e._v(" "),r("CHeaderNav",{staticClass:"mr-4"},[r("CHeaderNavItem",{staticClass:"mx-2"},[r("Link",{staticClass:"c-header-nav-link",attrs:{href:"/logout",method:"post"}},[r("i",{staticClass:"mdi mdi-directions"}),e._v(" "),r("span",[e._v("Salir")])])],1)],1)],1)}),[],!1,null,null,null).exports,f={components:{Link:n.rU,TheSidebar:l,TheHeader:u},data:function(){return{}},methods:{},props:{title:String},created:function(){}};const d=(0,i.Z)(f,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"c-app"},[r("TheSidebar"),e._v(" "),r("CWrapper",[r("TheHeader"),e._v(" "),r("div",{staticClass:"c-body"},[r("main",{staticClass:"c-main"},[r("CContainer",{attrs:{fluid:""}},[r("transition",{attrs:{name:"fade",mode:"out-in"}},[e._t("default")],2)],1)],1)])],1)],1)}),[],!1,null,null,null).exports},6118:(e,t,r)=>{"use strict";r.d(t,{Z:()=>o});const n={props:{loading:Boolean,text:String,textLoad:String,variant:String,size:String}};const o=(0,r(1900).Z)(n,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("b-button",{attrs:{variant:e.variant?e.variant:"primary",size:e.size?e.size:"",disabled:e.loading}},[e.loading?r("span",[r("b-spinner",{attrs:{small:""}}),e._v("\n        "+e._s(e.textLoad)+"...\n      ")],1):r("span",[e._v("\n        "+e._s(e.text)+"\n      ")])])}),[],!1,null,null,null).exports},4832:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});var n=r(6454);const o={props:{active:Number,links:Array},components:{Link:n.rU},methods:{isActive:function(e){return"nav-link"+(this.active===e?" active":"")}}};const a=(0,r(1900).Z)(o,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("ul",{staticClass:"nav nav-tabs"},[e._l(e.links,(function(t){return[r("li",{staticClass:"nav-item"},[r("Link",{class:e.isActive(t.active),attrs:{href:t.url}},[e._v(e._s(t.text))])],1)]}))],2)}),[],!1,null,null,null).exports},1900:(e,t,r)=>{"use strict";function n(e,t,r,n,o,a,s,i){var l,c="function"==typeof e?e.options:e;if(t&&(c.render=t,c.staticRenderFns=r,c._compiled=!0),n&&(c.functional=!0),a&&(c._scopeId="data-v-"+a),s?(l=function(e){(e=e||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(e=__VUE_SSR_CONTEXT__),o&&o.call(this,e),e&&e._registeredComponents&&e._registeredComponents.add(s)},c._ssrRegister=l):o&&(l=i?function(){o.call(this,(c.functional?this.parent:this).$root.$options.shadowRoot)}:o),l)if(c.functional){c._injectStyles=l;var u=c.render;c.render=function(e,t){return l.call(t),u(e,t)}}else{var f=c.beforeCreate;c.beforeCreate=f?[].concat(f,l):[l]}return{exports:e,options:c}}r.d(t,{Z:()=>n})}}]);