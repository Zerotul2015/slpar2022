<template>
  <div class="wrapper-content">
    <h1>Макеты страниц</h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="layout-add">
      <div class="form-section">
        <div class="input-block">
          <label for="template-for">Макеты для раздела: </label>
          <select class="select" id="template-for" v-model="selectedTypeLayout">
            <option v-for="(nameLayout, keyLayout) in nameLayoutFor" :value="keyLayout">{{ nameLayout }}</option>
          </select>
        </div>
        <div class="input-block">
          <label for="template-for">Элемент раздела: </label>
          <select class="select" v-model="selectedLayoutForID">
            <option value="0">макет по умолчани.</option>
            <option v-for="itemTarget in optionsLayoutForID" :value="itemTarget.id">
              {{ itemTarget.name }}
            </option>
          </select>
        </div>
      </div>
      <button class="button button_small" @click="addLayout" :disabled="disableAddButton" v-html="textButtonAdd">
        добавить макет
      </button>
    </div>
    <div v-if="layouts">
      <SettingsLayoutsItem v-for="(layoutItem, keyLayout) in layouts"
                           v-bind:layout="layoutItem"
                           v-bind:key="$root.guid()"
                           v-on:remove-layout="removeLayout(keyLayout)"
      >
      </SettingsLayoutsItem>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import api from '../../common/api'
import SettingsLayoutsItem from "./SettingsLayoutsItem.vue";

export default {
  name: "SettingsLayouts",
  components: {
    SettingsLayoutsItem
  },
  data() {
    return {
      loading: false,
      error: null,
      layouts: [],
      defaultLayoutKey: null,
      layoutExist: false,
      nameLayoutFor: {
        1: 'Категории товаров',
        //2: 'Подборки товаров',
        3: 'Страницы',
      },
      typeElement: {
        1: 'Баннер',
        2: 'Подборка товара',
        3: 'Произвольный HTML-блок',
        4: 'Страница'
      },
      slotsElementUsed: { // использует слотов 1 элемент
        1: 2,
        2: 6,
        3: 1,
        4: 1,
      },
      slotsAvailable: 6,// количество колонок,
      selectedTypeLayout: 1,
      selectedLayoutForID: 0,
      colorStyle: {
        error: 'rgba(255, 61, 61, 0.4)',
        success: 'rgba(131, 228, 146, 0.4)'
      }
    }
  },
  created() {
    // получаем данные
    this.$store.dispatch('productCategory/getAllById');
    this.$store.dispatch('banners/getAllById');
    this.$store.dispatch('page/getAllById');
    this.$store.dispatch('galleryCategory/getAllById');
    this.getLayouts(this.selectedTypeLayout);
  },
  watch: {
    layouts: function (newVal) {
      let that = this;
      newVal.forEach(function (layout, indexLayout) {
        if (layout.is_default) {
          that.defaultLayoutKey = indexLayout;
        }
        if (layout.for_id === that.selectedLayoutForID) {
          that.layoutExist = true;
        }
      });
    },
    selectedTypeLayout: function (newType) {
      this.getLayouts(newType);

    },
    selectedLayoutForID: function (newVal) {
      this.selectedLayoutForID = parseInt(newVal);
      this.checkUsedLayout();
    }
  },
  computed: {
    productsCategory() {
      return this.$store.state.productCategory.allById;
    },
    banners() {
      return this.$store.state.banners.allById;
    },
    galleryCategory() {
      return this.$store.state.galleryCategory.allById;
    },
    pages() {
      return this.$store.state.pages.allById;
    },
    textButtonAdd: function () {
      let returnText = 'добавить макет';
      if (this.layoutExist) {
        returnText = 'такой макет уже существует';
      }
      return returnText;
    },
    disableAddButton: function () {
      let disableButton = false;
      //если для выбранного обекта уже есть макет
      if (this.layoutExist) {
        disableButton = true;
      }

      return disableButton;
    },
    optionsLayoutForID: function () {
      let returnArray = [];
      switch (parseInt(this.selectedTypeLayout)) {
        case 1:
          returnArray = this.sortCategoriesAndGroupByParent();
          break;
        case 2:
          // returnArray = this.prepareValuesForOptionsTarget(this.productsCollections);
          break;
        case 3:
          returnArray = this.prepareValuesForOptionsTarget(this.pages, 'title');
          break;
        case 7:
          returnArray = this.prepareValuesForOptionsTarget(this.galleryCategory, 'category_name');
          break;
        default:
          break;
      }
      return returnArray;
    }
  },
  methods: {
    checkUsedLayout: function () {
      let that = this;
      let exist = false;
      this.layouts.forEach(function (layout, indexLayout) {
        if (layout.for_id === that.selectedLayoutForID) {
          exist = true;
        }
      });
      that.layoutExist = exist;
    },
    getLayouts: function (typeLayout) {
      this.loading = true;
      api.getData('layouts', {
        'where': 'layout_for',
        'searchString': typeLayout
      })
          .then((r) => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.layouts = r.returnData;
            } else {
              this.error = r.error ? r.error : 'неизвестная ошибка при получении макетов';
            }
          })
          .catch((error) => {
            this.error = error;
            this.loading = false;
          })
    },
    prepareValuesForOptionsTarget: function (arrayValues, keyName, keyID) {
      if (keyName === undefined) {
        keyName = 'name';
      }
      if (keyID === undefined) {
        keyID = 'id';
      }
      let returnOptions = [];
      for (let key in arrayValues) {
        // check also if property is not inherited from prototype
        if (arrayValues.hasOwnProperty(key)) {
          returnOptions.push({
            'id': arrayValues[key][keyID],
            'name': arrayValues[key][keyName],
          });
        }
      }
      return returnOptions;
    },
    sortCategoriesAndGroupByParent: function () {
      let that = this;
      let categoriesGroupParent = {};
      let cloneCategories = this.productsCategory;
      //сначало сгруппируем по родителям
      for (let key in cloneCategories) {
        // check also if property is not inherited from prototype
        if (cloneCategories.hasOwnProperty(key)) {
          if (categoriesGroupParent[cloneCategories[key].parent_id]) {
            categoriesGroupParent[cloneCategories[key].parent_id].push(cloneCategories[key]);
          } else {
            categoriesGroupParent[cloneCategories[key].parent_id] = [];
            categoriesGroupParent[cloneCategories[key].parent_id].push(cloneCategories[key]);
          }
        }
      }
      let categoriesForRender = [];
      for (let key in categoriesGroupParent) {
        // check also if property is not inherited from prototype
        if (categoriesGroupParent.hasOwnProperty(key)) {

          if (key == 0) {
            let categoryMainArrayObjects = categoriesGroupParent[key];

            for (let key2 in categoryMainArrayObjects) {
              if (categoryMainArrayObjects.hasOwnProperty(key2)) {
                categoriesForRender.push(categoryMainArrayObjects[key2]);
                if (categoriesGroupParent[categoryMainArrayObjects[key2].id]) {
                  let subCategories = categoriesGroupParent[categoryMainArrayObjects[key2].id];
                  for (let key3 in subCategories) {
                    if (subCategories.hasOwnProperty(key3)) {
                      subCategories[key3].name = '- ' + subCategories[key3].name;
                      categoriesForRender.push(subCategories[key3]);
                    }
                  }
                }
              }
            }
          }

        }
      }
      return categoriesForRender;
    },
    addLayout: function () {
      let isDefault = 0;
      if (!this.selectedLayoutForID) {
        isDefault = 1;
      }
      let layoutsNew = {
        'layout_for': this.selectedTypeLayout,
        'is_default': isDefault,
        'for_id': this.selectedLayoutForID,
        'blocks': {
          'topBlocks': [],
          'sidebarBlocks': [],
          'bottomBlocks': [],
        },
      };
      this.layouts.push(layoutsNew);
    },
    removeLayout: async function (keyLayout) {
      if (this.layouts[keyLayout]) {
        if (this.layouts[keyLayout].id) {
          await api.applyData('layouts', 'delete', {id: this.layouts[keyLayout].id})
              .then((r) => {
                if (r.result && r.result === true) {
                  this.layouts.splice(keyLayout, 1);
                } else {
                  this.error = r.error ? r.error : 'неизвестная ошибка при удалении макета';
                }
              })
              .catch((e) => {
                this.error = e;
              })
        } else {
          delete this.layouts[keyLayout];
        }
      }
    },
  }
}
</script>

<style scoped>

</style>