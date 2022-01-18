/*! For license information please see app.resources_js_admin_components_Pages_PagesCategoriesCreate_vue.7ccc33ee85c82584711b.js.LICENSE.txt */
"use strict";(self.webpackChunkmarketplace=self.webpackChunkmarketplace||[]).push([["resources_js_admin_components_Pages_PagesCategoriesCreate_vue"],{"./node_modules/babel-loader/lib/index.js??clonedRuleSet-1[0].rules[0].use!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=script&lang=js&":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _common_api__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../common/api */ \"./resources/js/admin/common/api.js\");\nfunction asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }\n\nfunction _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"next\", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, \"throw\", err); } _next(undefined); }); }; }\n\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n//\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({\n  name: \"PagesCategoriesCreate\",\n  props: {\n    id: {}\n  },\n  data: function data() {\n    return {\n      loading: false,\n      error: null,\n      category: {},\n      dateVal: null,\n      errorUploadText: null,\n      //текст ошибки загрузки\n      errorSaveText: null,\n      //текст ошибки при  сохранения\n      errorDeleteText: null,\n      //текст ошибки удаления\n      //start save, delete\n      saveStatus: null,\n      // 1 -  успешно, 2 - ошибка, 0 | null - без изменений\n      saveButtonDefault: '<span class=\"button-icon\"><i class=\"far fa-save\"></i></span><span class=\"button-icon\">сохарнить</span>',\n      saveButtonSuccess: '<span class=\"button-icon\"><i class=\"far fa-check\"></i></span><span class=\"button-icon\">изменения записаны</span>',\n      saveButtonError: '<span class=\"button-icon\"><i class=\"far fa-times\"></i></span><span class=\"button-icon\">ошибка при сохранении</span>',\n      deleteStatus: null,\n      // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений\n      deleteButtonDefault: '<span class=\"button-icon\"><i class=\"far fa-trash-alt\"></i></span><span class=\"button-icon\">удалить</span>',\n      deleteButtonConfirm: '<span class=\"button-icon\"><i class=\"far fa-trash-alt\"></i></span><span class=\"button-icon\">подтвердить удаление</span>',\n      deleteButtonError: '<span class=\"button-icon\"><i class=\"far fa-trash-alt\"></i></span><span class=\"button-icon\">ошибка при удалии</span>' //end save, delete\n\n    };\n  },\n  mounted: function mounted() {\n    if (this.id) {\n      this.getCategory();\n    } else {\n      this.category = {\n        name_short: '',\n        name_full: '',\n        description: '',\n        date: null,\n        seo: {\n          description: '',\n          title: ''\n        },\n        url: null\n      };\n    }\n  },\n  watch: {\n    'category.date': function categoryDate(newVal) {\n      if (newVal !== this.dateVal) {\n        this.dateVal = new Date(Date.parse(newVal)).toLocaleDateString('en-CA');\n      }\n    },\n    dateVal: function dateVal(newVal) {\n      if (newVal !== this.category.date) {\n        this.category.date = new Date(Date.parse(newVal)).toLocaleDateString('en-CA');\n      }\n    },\n    category: {\n      deep: true,\n      handler: function handler(newVal, oldVal) {}\n    },\n    //start save, delete\n    saveStatus: function saveStatus(newVal) {\n      var _this = this;\n\n      if (newVal) {\n        setTimeout(function () {\n          _this.saveStatus = null;\n        }, 2500);\n      }\n    },\n    deleteStatus: function deleteStatus(newVal) {\n      var _this2 = this;\n\n      if (newVal === 2) {\n        setTimeout(function () {\n          _this2.saveStatus = null;\n        }, 2500);\n      }\n    } //end save, delete\n\n  },\n  computed: {\n    buttonToggleSeo: function buttonToggleSeo() {\n      return !this.toggleSeo ? 'Показать seo настройки' : 'скрыть seo настройки';\n    },\n    //start save, delete\n    saveButtonText: function saveButtonText() {\n      if (this.saveStatus === 1) {\n        return this.saveButtonSuccess;\n      }\n\n      if (this.saveStatus === 2) {\n        return this.saveButtonError;\n      }\n\n      if (this.saveStatus === 0 || this.saveStatus === null) {\n        return this.saveButtonDefault;\n      }\n    },\n    deleteButtonText: function deleteButtonText() {\n      if (this.deleteStatus === 1) {\n        return this.deleteButtonConfirm;\n      }\n\n      if (this.deleteStatus === 2) {\n        return this.deleteButtonError;\n      }\n\n      if (this.deleteStatus === 0 || this.deleteStatus === null) {\n        return this.deleteButtonDefault;\n      }\n    } //end save, delete\n\n  },\n  methods: {\n    //end image upload\n    getCategory: function () {\n      var _getCategory = _asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee() {\n        var _this3 = this;\n\n        return regeneratorRuntime.wrap(function _callee$(_context) {\n          while (1) {\n            switch (_context.prev = _context.next) {\n              case 0:\n                _common_api__WEBPACK_IMPORTED_MODULE_0__[\"default\"].getData('pageCategory', {\n                  'where': 'id',\n                  'searchString': this.id\n                }).then(function (r) {\n                  _this3.loading = false;\n\n                  if (r.result && r.result === true) {\n                    _this3.category = r.returnData[0];\n                  }\n\n                  if (r.error) {\n                    _this3.error = 'Во время получения данных категории возникла ошибка: ' + r.error ? r.error : 0;\n                  }\n                })[\"catch\"](function (e) {\n                  _this3.loading = false;\n                  _this3.error = 'Во время получения данных категории возникла ошибка: ' + e ? e : 0;\n                });\n\n              case 1:\n              case \"end\":\n                return _context.stop();\n            }\n          }\n        }, _callee, this);\n      }));\n\n      function getCategory() {\n        return _getCategory.apply(this, arguments);\n      }\n\n      return getCategory;\n    }(),\n    saveCategory: function () {\n      var _saveCategory = _asyncToGenerator( /*#__PURE__*/regeneratorRuntime.mark(function _callee2() {\n        var _this4 = this;\n\n        return regeneratorRuntime.wrap(function _callee2$(_context2) {\n          while (1) {\n            switch (_context2.prev = _context2.next) {\n              case 0:\n                _common_api__WEBPACK_IMPORTED_MODULE_0__[\"default\"].applyData('pageCategory', 'save', this.category).then(function (r) {\n                  if (r.result === true) {\n                    _this4.saveStatus = 1;\n\n                    _this4.$router.push({\n                      'name': 'PagesCategories'\n                    });\n                  } else {\n                    _this4.saveStatus = 2;\n                    _this4.errorSaveText = r.error ? r.error : 'неизвестная ошибка: ' + r;\n                  }\n                })[\"catch\"](function (e) {\n                  _this4.saveStatus = 2;\n                  _this4.errorSaveText = e.error ? e.error : 'неизвестная ошибка: ' + e;\n                });\n\n              case 1:\n              case \"end\":\n                return _context2.stop();\n            }\n          }\n        }, _callee2, this);\n      }));\n\n      function saveCategory() {\n        return _saveCategory.apply(this, arguments);\n      }\n\n      return saveCategory;\n    }(),\n    deleteCategory: function deleteCategory() {\n      var _this5 = this;\n\n      if (this.deleteStatus === 1) {\n        _common_api__WEBPACK_IMPORTED_MODULE_0__[\"default\"].applyData('pageCategory', 'delete', {\n          'id': this.category.id\n        }).then(function (r) {\n          if (r.result === true) {\n            _this5.$router.push({\n              'name': 'PagesCategories'\n            });\n          } else {\n            _this5.deleteStatus = 2;\n            _this5.errorDeleteText = r.error ? r.error : 'неизвестная ошибка: ' + r;\n          }\n        })[\"catch\"](function (e) {\n          _this5.deleteStatus = 2;\n          _this5.errorDeleteText = e.error ? e.error : 'неизвестная ошибка: ' + e;\n        });\n      } else {\n        this.deleteStatus = 1;\n      }\n    }\n  }\n});\n\n//# sourceURL=webpack://marketplace/./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-1%5B0%5D.rules%5B0%5D.use!./node_modules/vue-loader/lib/index.js??vue-loader-options")},"./resources/js/admin/components/Pages/PagesCategoriesCreate.vue":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _PagesCategoriesCreate_vue_vue_type_template_id_2d31c496_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./PagesCategoriesCreate.vue?vue&type=template&id=2d31c496&scoped=true& */ "./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=template&id=2d31c496&scoped=true&");\n/* harmony import */ var _PagesCategoriesCreate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./PagesCategoriesCreate.vue?vue&type=script&lang=js& */ "./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=script&lang=js&");\n/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");\n\n\n\n\n\n/* normalize component */\n;\nvar component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(\n  _PagesCategoriesCreate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],\n  _PagesCategoriesCreate_vue_vue_type_template_id_2d31c496_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render,\n  _PagesCategoriesCreate_vue_vue_type_template_id_2d31c496_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,\n  false,\n  null,\n  "2d31c496",\n  null\n  \n)\n\n/* hot reload */\nif (false) { var api; }\ncomponent.options.__file = "resources/js/admin/components/Pages/PagesCategoriesCreate.vue"\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);\n\n//# sourceURL=webpack://marketplace/./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?')},"./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=script&lang=js&":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_1_0_rules_0_use_node_modules_vue_loader_lib_index_js_vue_loader_options_PagesCategoriesCreate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-1[0].rules[0].use!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./PagesCategoriesCreate.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-1[0].rules[0].use!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=script&lang=js&");\n /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_1_0_rules_0_use_node_modules_vue_loader_lib_index_js_vue_loader_options_PagesCategoriesCreate_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); \n\n//# sourceURL=webpack://marketplace/./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?')},"./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=template&id=2d31c496&scoped=true&":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PagesCategoriesCreate_vue_vue_type_template_id_2d31c496_scoped_true___WEBPACK_IMPORTED_MODULE_0__.render),\n/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PagesCategoriesCreate_vue_vue_type_template_id_2d31c496_scoped_true___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)\n/* harmony export */ });\n/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PagesCategoriesCreate_vue_vue_type_template_id_2d31c496_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./PagesCategoriesCreate.vue?vue&type=template&id=2d31c496&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=template&id=2d31c496&scoped=true&");\n\n\n//# sourceURL=webpack://marketplace/./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?')},"./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?vue&type=template&id=2d31c496&scoped=true&":(__unused_webpack_module,__webpack_exports__,__webpack_require__)=>{eval('__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   "render": () => (/* binding */ render),\n/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)\n/* harmony export */ });\nvar render = function () {\n  var _vm = this\n  var _h = _vm.$createElement\n  var _c = _vm._self._c || _h\n  return _c("div", { staticClass: "form" }, [\n    _c("h1", [\n      _vm.category.id\n        ? _c("span", [_vm._v("Редактирование")])\n        : _c("span", [_vm._v("Создание")]),\n      _vm._v(" категории "),\n      _vm.category.title\n        ? _c("span", { domProps: { innerHTML: _vm._s(_vm.category.title) } })\n        : _vm._e(),\n    ]),\n    _vm._v(" "),\n    _vm.loading\n      ? _c("div", { staticClass: "loading" }, [_vm._v("\\n    Загрузка...\\n  ")])\n      : _vm._e(),\n    _vm._v(" "),\n    _vm.error\n      ? _c("div", { staticClass: "error" }, [\n          _vm._v("\\n    " + _vm._s(_vm.error) + "\\n  "),\n        ])\n      : _vm._e(),\n    _vm._v(" "),\n    _c("div", { staticClass: "form-section" }, [\n      _c("div", { staticClass: "buttons-block" }, [\n        _c("button", {\n          staticClass: "button button_green",\n          domProps: { innerHTML: _vm._s(_vm.saveButtonText) },\n          on: { click: _vm.saveCategory },\n        }),\n        _vm._v(" "),\n        _vm.category.id && !_vm.category.integrated\n          ? _c("button", {\n              staticClass: "button button_remove",\n              domProps: { innerHTML: _vm._s(_vm.deleteButtonText) },\n              on: { click: _vm.deleteCategory },\n            })\n          : _vm._e(),\n        _vm._v(" "),\n        _c("br"),\n        _vm._v(" "),\n        _c("div", {\n          staticClass: "error-description",\n          domProps: { innerHTML: _vm._s(_vm.errorSaveText) },\n        }),\n        _vm._v(" "),\n        _c("div", {\n          staticClass: "error-description",\n          domProps: { innerHTML: _vm._s(_vm.errorDeleteText) },\n        }),\n      ]),\n    ]),\n    _vm._v(" "),\n    _c("div", { staticClass: "form-section" }, [\n      _c("div", { staticClass: "input-block input-block_highlight" }, [\n        _c("label", { attrs: { for: "seo-title" } }, [\n          _vm._v("Seo заголовок:"),\n        ]),\n        _vm._v(" "),\n        _c("input", {\n          directives: [\n            {\n              name: "model",\n              rawName: "v-model",\n              value: _vm.category.seo.title,\n              expression: "category.seo.title",\n            },\n          ],\n          staticClass: "input",\n          attrs: { id: "seo-title", type: "text" },\n          domProps: { value: _vm.category.seo.title },\n          on: {\n            input: function ($event) {\n              if ($event.target.composing) {\n                return\n              }\n              _vm.$set(_vm.category.seo, "title", $event.target.value)\n            },\n          },\n        }),\n      ]),\n      _vm._v(" "),\n      _c(\n        "div",\n        { staticClass: "input-block input-block_column input-block_highlight" },\n        [\n          _c("label", { attrs: { for: "seo-description" } }, [\n            _vm._v("Seo описание:"),\n          ]),\n          _vm._v(" "),\n          _c("textarea", {\n            directives: [\n              {\n                name: "model",\n                rawName: "v-model",\n                value: _vm.category.seo.description,\n                expression: "category.seo.description",\n              },\n            ],\n            staticClass: "textarea",\n            attrs: { id: "seo-description" },\n            domProps: { value: _vm.category.seo.description },\n            on: {\n              input: function ($event) {\n                if ($event.target.composing) {\n                  return\n                }\n                _vm.$set(_vm.category.seo, "description", $event.target.value)\n              },\n            },\n          }),\n        ]\n      ),\n    ]),\n    _vm._v(" "),\n    _c("div", { staticClass: "form-section" }, [\n      _c("div", { staticClass: "input-block input-block_highlight" }, [\n        _c("label", { attrs: { for: "date-page" } }, [\n          _vm._v("Дата публикации:"),\n        ]),\n        _vm._v(" "),\n        _c("input", {\n          directives: [\n            {\n              name: "model",\n              rawName: "v-model",\n              value: _vm.dateVal,\n              expression: "dateVal",\n            },\n          ],\n          staticClass: "input",\n          attrs: { id: "date-page", type: "date" },\n          domProps: { value: _vm.dateVal },\n          on: {\n            input: function ($event) {\n              if ($event.target.composing) {\n                return\n              }\n              _vm.dateVal = $event.target.value\n            },\n          },\n        }),\n      ]),\n      _vm._v(" "),\n      _c("div", { staticClass: "input-block input-block_highlight" }, [\n        _c("label", { attrs: { for: "name-short" } }, [\n          _vm._v("Название короткое:"),\n        ]),\n        _vm._v(" "),\n        _c("input", {\n          directives: [\n            {\n              name: "model",\n              rawName: "v-model",\n              value: _vm.category.name_short,\n              expression: "category.name_short",\n            },\n          ],\n          staticClass: "input",\n          attrs: { id: "name-short", type: "text" },\n          domProps: { value: _vm.category.name_short },\n          on: {\n            input: function ($event) {\n              if ($event.target.composing) {\n                return\n              }\n              _vm.$set(_vm.category, "name_short", $event.target.value)\n            },\n          },\n        }),\n      ]),\n      _vm._v(" "),\n      _c("div", { staticClass: "input-block input-block_highlight" }, [\n        _c("label", { attrs: { for: "name-full" } }, [\n          _vm._v("Название полное:"),\n        ]),\n        _vm._v(" "),\n        _c("input", {\n          directives: [\n            {\n              name: "model",\n              rawName: "v-model",\n              value: _vm.category.name_full,\n              expression: "category.name_full",\n            },\n          ],\n          staticClass: "input",\n          attrs: { id: "name-full", type: "text" },\n          domProps: { value: _vm.category.name_full },\n          on: {\n            input: function ($event) {\n              if ($event.target.composing) {\n                return\n              }\n              _vm.$set(_vm.category, "name_full", $event.target.value)\n            },\n          },\n        }),\n      ]),\n      _vm._v(" "),\n      _c(\n        "div",\n        { staticClass: "input-block input-block_column input-block_highlight" },\n        [\n          _c("label", { attrs: { for: "description" } }, [_vm._v("Описание:")]),\n          _vm._v(" "),\n          _c("textarea", {\n            directives: [\n              {\n                name: "model",\n                rawName: "v-model",\n                value: _vm.category.description,\n                expression: "category.description",\n              },\n            ],\n            staticClass: "textarea",\n            attrs: { id: "description" },\n            domProps: { value: _vm.category.description },\n            on: {\n              input: function ($event) {\n                if ($event.target.composing) {\n                  return\n                }\n                _vm.$set(_vm.category, "description", $event.target.value)\n              },\n            },\n          }),\n        ]\n      ),\n    ]),\n  ])\n}\nvar staticRenderFns = []\nrender._withStripped = true\n\n\n\n//# sourceURL=webpack://marketplace/./resources/js/admin/components/Pages/PagesCategoriesCreate.vue?./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options')}}]);