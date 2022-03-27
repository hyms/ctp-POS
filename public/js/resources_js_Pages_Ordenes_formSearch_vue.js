"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_Pages_Ordenes_formSearch_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/Pages/Ordenes/formSearch.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/Pages/Ordenes/formSearch.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data: function data() {
    return {
      searchModel: {
        fechaI: "",
        fechaF: "",
        orden: "",
        tipo: "",
        responsable: "",
        estado: ""
      },
      total: 0,
      cardCollapse: false
    };
  },
  props: {
    report: Object,
    estados: Object,
    tiposSelect: Object
  },
  mounted: function mounted() {
    for (var key in this.report) {
      this.searchModel[key] = this.report[key];
    }
  }
});

/***/ }),

/***/ "./resources/js/Pages/Ordenes/formSearch.vue":
/*!***************************************************!*\
  !*** ./resources/js/Pages/Ordenes/formSearch.vue ***!
  \***************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _formSearch_vue_vue_type_template_id_70ee346c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./formSearch.vue?vue&type=template&id=70ee346c& */ "./resources/js/Pages/Ordenes/formSearch.vue?vue&type=template&id=70ee346c&");
/* harmony import */ var _formSearch_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./formSearch.vue?vue&type=script&lang=js& */ "./resources/js/Pages/Ordenes/formSearch.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _formSearch_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _formSearch_vue_vue_type_template_id_70ee346c___WEBPACK_IMPORTED_MODULE_0__.render,
  _formSearch_vue_vue_type_template_id_70ee346c___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/Pages/Ordenes/formSearch.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/Pages/Ordenes/formSearch.vue?vue&type=script&lang=js&":
/*!****************************************************************************!*\
  !*** ./resources/js/Pages/Ordenes/formSearch.vue?vue&type=script&lang=js& ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_formSearch_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./formSearch.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/Pages/Ordenes/formSearch.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_formSearch_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/Pages/Ordenes/formSearch.vue?vue&type=template&id=70ee346c&":
/*!**********************************************************************************!*\
  !*** ./resources/js/Pages/Ordenes/formSearch.vue?vue&type=template&id=70ee346c& ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_formSearch_vue_vue_type_template_id_70ee346c___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_formSearch_vue_vue_type_template_id_70ee346c___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_formSearch_vue_vue_type_template_id_70ee346c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./formSearch.vue?vue&type=template&id=70ee346c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/Pages/Ordenes/formSearch.vue?vue&type=template&id=70ee346c&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/Pages/Ordenes/formSearch.vue?vue&type=template&id=70ee346c&":
/*!*************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/Pages/Ordenes/formSearch.vue?vue&type=template&id=70ee346c& ***!
  \*************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _c(
    "CCard",
    [
      _c(
        "CButton",
        {
          staticClass: "text-left shadow-none card-header",
          attrs: { tag: "button", color: "link", block: "" },
          on: {
            click: function ($event) {
              _vm.cardCollapse = !_vm.cardCollapse
            },
          },
        },
        [_c("h5", { staticClass: "m-0" }, [_vm._v("Filtros")])]
      ),
      _vm._v(" "),
      _c(
        "CCollapse",
        { attrs: { show: _vm.cardCollapse } },
        [
          _c("CCardBody", { staticClass: "m-1" }, [
            _c("div", { staticClass: "row" }, [
              _c("div", { staticClass: "col" }, [
                _c(
                  "form",
                  { staticClass: "form-inline", attrs: { method: "get" } },
                  [
                    _c(
                      "div",
                      { staticClass: "col-sm-5" },
                      [
                        _c(
                          "b-form-group",
                          {
                            attrs: {
                              label: "Fecha",
                              "label-for": "fecha Desde",
                              "label-cols": "4",
                              "content-cols": "8",
                            },
                          },
                          [
                            _c("b-input", {
                              attrs: {
                                type: "date",
                                placeholder: "fecha",
                                id: "fechaI",
                                name: "fechaI",
                              },
                              model: {
                                value: _vm.searchModel.fechaI,
                                callback: function ($$v) {
                                  _vm.$set(_vm.searchModel, "fechaI", $$v)
                                },
                                expression: "searchModel.fechaI",
                              },
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-sm-5" },
                      [
                        _c(
                          "b-form-group",
                          {
                            attrs: {
                              label: "hasta",
                              "label-for": "fecha",
                              "label-cols": "4",
                              "content-cols": "8",
                            },
                          },
                          [
                            _c("b-input", {
                              attrs: {
                                type: "date",
                                placeholder: "fecha hasta",
                                id: "fechaF",
                                name: "fechaF",
                              },
                              model: {
                                value: _vm.searchModel.fechaF,
                                callback: function ($$v) {
                                  _vm.$set(_vm.searchModel, "fechaF", $$v)
                                },
                                expression: "searchModel.fechaF",
                              },
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-sm-5" },
                      [
                        _c(
                          "b-form-group",
                          {
                            attrs: {
                              label: "Codigo",
                              "label-for": "orden",
                              "label-cols": "4",
                              "content-cols": "8",
                            },
                          },
                          [
                            _c("b-input", {
                              attrs: {
                                type: "text",
                                placeholder: "Codigo",
                                id: "orden",
                                name: "orden",
                              },
                              model: {
                                value: _vm.searchModel.orden,
                                callback: function ($$v) {
                                  _vm.$set(_vm.searchModel, "orden", $$v)
                                },
                                expression: "searchModel.orden",
                              },
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-sm-5" },
                      [
                        _c(
                          "b-form-group",
                          {
                            attrs: {
                              label: "Cliente",
                              "label-for": "responsable",
                              "label-cols": "4",
                              "content-cols": "8",
                            },
                          },
                          [
                            _c("b-input", {
                              attrs: {
                                type: "text",
                                placeholder: "Cliente",
                                id: "responsable",
                                name: "responsable",
                              },
                              model: {
                                value: _vm.searchModel.responsable,
                                callback: function ($$v) {
                                  _vm.$set(_vm.searchModel, "responsable", $$v)
                                },
                                expression: "searchModel.responsable",
                              },
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-sm-5" },
                      [
                        _c(
                          "b-form-group",
                          {
                            attrs: {
                              label: "Estado",
                              "label-for": "estado",
                              "label-cols": "4",
                              "content-cols": "8",
                            },
                          },
                          [
                            _c("b-select", {
                              attrs: {
                                placeholder: "Estado",
                                id: "estado",
                                name: "estado",
                                options: _vm.estados,
                              },
                              model: {
                                value: _vm.searchModel.estado,
                                callback: function ($$v) {
                                  _vm.$set(_vm.searchModel, "estado", $$v)
                                },
                                expression: "searchModel.estado",
                              },
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-sm-5" },
                      [
                        _c(
                          "b-form-group",
                          {
                            attrs: {
                              label: "Tipo",
                              "label-for": "tipo",
                              "label-cols": "4",
                              "content-cols": "8",
                            },
                          },
                          [
                            _c("b-select", {
                              attrs: {
                                placeholder: "Tipo",
                                id: "tipo",
                                name: "tipo",
                                options: _vm.tiposSelect,
                              },
                              model: {
                                value: _vm.searchModel.tipo,
                                callback: function ($$v) {
                                  _vm.$set(_vm.searchModel, "tipo", $$v)
                                },
                                expression: "searchModel.tipo",
                              },
                            }),
                          ],
                          1
                        ),
                      ],
                      1
                    ),
                    _vm._v(" "),
                    _vm.searchModel.total
                      ? _c("div", { staticClass: "col-12 text-center mt-1" }, [
                          _c("h5", [
                            _c("strong", [_vm._v("Total:")]),
                            _vm._v(" " + _vm._s(_vm.searchModel.total)),
                          ]),
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-12 text-center mt-1" },
                      [
                        _c(
                          "b-button",
                          {
                            attrs: {
                              size: "sm",
                              type: "submit",
                              variant: "primary",
                            },
                          },
                          [_vm._v("Buscar")]
                        ),
                      ],
                      1
                    ),
                  ]
                ),
              ]),
            ]),
          ]),
        ],
        1
      ),
    ],
    1
  )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js":
/*!********************************************************************!*\
  !*** ./node_modules/vue-loader/lib/runtime/componentNormalizer.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ normalizeComponent)
/* harmony export */ });
/* globals __VUE_SSR_CONTEXT__ */

// IMPORTANT: Do NOT use ES2015 features in this file (except for modules).
// This module is a runtime utility for cleaner component module output and will
// be included in the final webpack user bundle.

function normalizeComponent (
  scriptExports,
  render,
  staticRenderFns,
  functionalTemplate,
  injectStyles,
  scopeId,
  moduleIdentifier, /* server only */
  shadowMode /* vue-cli only */
) {
  // Vue.extend constructor export interop
  var options = typeof scriptExports === 'function'
    ? scriptExports.options
    : scriptExports

  // render functions
  if (render) {
    options.render = render
    options.staticRenderFns = staticRenderFns
    options._compiled = true
  }

  // functional template
  if (functionalTemplate) {
    options.functional = true
  }

  // scopedId
  if (scopeId) {
    options._scopeId = 'data-v-' + scopeId
  }

  var hook
  if (moduleIdentifier) { // server build
    hook = function (context) {
      // 2.3 injection
      context =
        context || // cached call
        (this.$vnode && this.$vnode.ssrContext) || // stateful
        (this.parent && this.parent.$vnode && this.parent.$vnode.ssrContext) // functional
      // 2.2 with runInNewContext: true
      if (!context && typeof __VUE_SSR_CONTEXT__ !== 'undefined') {
        context = __VUE_SSR_CONTEXT__
      }
      // inject component styles
      if (injectStyles) {
        injectStyles.call(this, context)
      }
      // register component module identifier for async chunk inferrence
      if (context && context._registeredComponents) {
        context._registeredComponents.add(moduleIdentifier)
      }
    }
    // used by ssr in case component is cached and beforeCreate
    // never gets called
    options._ssrRegister = hook
  } else if (injectStyles) {
    hook = shadowMode
      ? function () {
        injectStyles.call(
          this,
          (options.functional ? this.parent : this).$root.$options.shadowRoot
        )
      }
      : injectStyles
  }

  if (hook) {
    if (options.functional) {
      // for template-only hot-reload because in that case the render fn doesn't
      // go through the normalizer
      options._injectStyles = hook
      // register for functional component in vue file
      var originalRender = options.render
      options.render = function renderWithStyleInjection (h, context) {
        hook.call(context)
        return originalRender(h, context)
      }
    } else {
      // inject component registration as beforeCreate hook
      var existing = options.beforeCreate
      options.beforeCreate = existing
        ? [].concat(existing, hook)
        : [hook]
    }
  }

  return {
    exports: scriptExports,
    options: options
  }
}


/***/ })

}]);