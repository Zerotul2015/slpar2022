<template>
  <div class="layout-template-wrap">
    <div class="layout-template__title">
      <span v-html="titleLayout"></span>
      <div class="buttons-block">
        <button class="button button_small" @click="minimized = !minimized"
                v-html="buttonMinimized"></button>
        <button class="button button_small" @click="saveThisLayout"
                v-html="saveButtonText"></button>
        <button class="button button_small button_remove" @click="removeThisLayout"
                v-html="removeButton"></button>
      </div>
    </div>
    <div v-if="!minimized">
      <div class="layout-template">
        <div class="layout-template__header">
          <img class="layout-template__header__placeholder"
               src="/build/images/admin/layouts/header_layouts.jpg" title="Шапка сайта">
        </div>
        <div class="layout-template__top">
          <div class="layout-template__line__add">
            <button class="button button_small button_white ml-1" @click="addRow('top')">
              <span><i class="far fa-layer-plus"></i></span>
              <span>Добавить строку</span>
            </button>
          </div>
          <SettingsLayoutsItemRow v-for="(topBlock, numberRow) in topBlocks"
                                  v-bind:row-elements="topBlock"
                                  v-bind:key="$root.guid()"
                                  v-on:remove-row="removeRow('top', numberRow)">
          </SettingsLayoutsItemRow>
        </div>
        <div class="layout-template__sidebar">
          <div class="layout-template__line__add" v-if="!sidebarBlocks.length">
            <button class="button button_small button_white ml-1" @click="addRow('sidebar')">
              <span><i class="far fa-layer-plus"></i></span>
              <span>Добавить строку</span>
            </button>
          </div>
          <SettingsLayoutsItemRow v-for="(topBlock, numberRow) in sidebarBlocks"
                                  v-bind:row-elements="topBlock"
                                  :orientation="'vertical'"
                                  v-bind:key="$root.guid()"
                                  v-on:remove-row="removeRow('sidebar', numberRow)"
          >
          </SettingsLayoutsItemRow>
        </div>
        <div class="layout-template__content">
          <img class="layout-template__header__placeholder"
               :src="placeholderContent" title="Содержимое страницы сайта">
        </div>
        <div class="layout-template__footer">
          <div class="layout-template__line__add">
            <button class="button button_small button_white ml-1" @click="addRow('bottom')">
              <span><i class="far fa-layer-plus"></i></span>
              <span>Добавить строку</span>
            </button>
          </div>
          <SettingsLayoutsItemRow v-for="(topBlock, numberRow) in bottomBlocks"
                                  v-bind:row-elements="topBlock"
                                  v-bind:key="$root.guid()"
                                  v-on:remove-row="removeRow('bottom', numberRow)"
          >
          </SettingsLayoutsItemRow>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import SettingsLayoutsItemRow from "./SettingsLayoutsItemRow.vue"
import api from "../../common/api";

export default {
  name: "SettingsLayoutsItem",
  components: {
    SettingsLayoutsItemRow
  },
  props: {
    layout: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      topBlocks: this.layout.blocks.topBlocks,
      sidebarBlocks: this.layout.blocks.sidebarBlocks,
      bottomBlocks: this.layout.blocks.bottomBlocks,
      isDefault: false,
      confirmRemove: false,
      minimized: true,
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-text">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-text">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-text">ошибка при сохранении</span>',
      textError: ''
    }
  },
  created() {
    if (!this.layout.id) {
      this.minimized = false;
    }
    if (!this.layout.for_id || this.layout.is_default) {
      this.is_default = true;
    }
  },
  computed: {
    productsCategory() {
      return this.$store.state.productCategory.allById;
    },
    pages() {
      return this.$store.state.pages;
    },
    buttonMinimized: function () {
      let htmlButton = '<i class="far fa-chevron-double-up"></i> свернуть';
      if (this.minimized === true) {
        {
          htmlButton = '<i class="far fa-chevron-double-down"></i> развернуть';
        }
      }
      return htmlButton;
    },
    saveButtonText: function () {
      if (this.saveStatus === 1) {
        return this.saveButtonSuccess;
      }
      if (this.saveStatus === 2) {
        return this.saveButtonError;
      }
      if (this.saveStatus === 0 || this.saveStatus === null) {
        return this.saveButtonDefault;
      }
    },
    removeButton: function () {
      let htmlButton = '<i class="far fa-trash-alt"></i>';
      if (this.confirmRemove) {
        return htmlButton + ' подтвердить удаление';
      } else {
        return htmlButton + ' удалить';
      }
    },
    titleLayout: function () {
      let titleLayout = 'Макет для ';

      let textAfter = '';
      let layoutFor = parseInt(this.layout.layout_for);
      switch (layoutFor) {
        case 1:
          if (this.productsCategory[this.layout.for_id]) {
            textAfter = 'категории ' + this.productsCategory[this.layout.for_id].name;
            if (this.productsCategory[this.layout.for_id].parent_id) {
              let parentID = this.productsCategory[this.layout.for_id].parent_id;
              textAfter = textAfter + ' (' + this.productsCategory[parentID].name + ')';
            }
          } else {
            titleLayout = 'Макет <u><b>по молчанию</b></u> для категорий товаров';
          }
          break;
        case 2:

          break;
        case 3:
          console.log(this.pages[this.layout.for_id]);
          if (this.pages[this.layout.for_id]) {
            textAfter = ' страницы ' + this.pages[this.layout.for_id].title;
          } else {
            titleLayout = 'Макет <u><b>по молчанию</b></u> для страниц';
          }
          break;
        default:
          titleLayout = 'Макет по умолчанию';
          break;

      }

      return titleLayout + textAfter;
    },
    placeholderContent: function () {
      let imagePlaceholder = '/build/images/admin/layouts/content_layouts.jpg';
      //для категорий и подборок товаров
      if (this.layout.layout_for === 1 || this.layout.layout_for === 2) {
        imagePlaceholder = '/build/images/admin/layouts/content_layouts.jpg';
      }
      return imagePlaceholder;
    },
  },
  watch: {
    //start save, delete
    saveStatus: function (newVal) {
      if (newVal) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    //end save, delete
    topBlocks: function (newVal) {
      this.layout.blocks.topBlocks = newVal;
    },
    sidebarBlocks: function (newVal) {
      this.layout.blocks.sidebarBlocks = newVal;
    },
    bottomBlocks: function (newVal) {
      this.layout.blocks.bottomBlocks = newVal;
    },

  },
  methods: {
    addRow: function (blockName) {
      this[blockName + 'Blocks'].push([]);
    },
    removeRow: function (blockName, numberRow) {
      this[blockName + 'Blocks'].splice(numberRow, 1);
    },
    removeThisLayout: function () {
      if (this.confirmRemove) {
        this.$emit('remove-layout')
      } else {
        this.confirmRemove = true;
      }
    },
    saveThisLayout: async function () {
      await api.applyData('layouts', 'save', this.layout)
          .then((r)=>{
            if(r.result && r.result === true){
              this.saveStatus = 1;
              if(r.id){
                this.layout.id = r.id;
              }
            }else{
              this.saveStatus = 2;
              this.error = r.error ? r.error : 'неизвестная ошибка при сохранении макета';
            }
          })
          .catch((e)=>{
        this.saveStatus = 2;
        this.error = e;
      });
    }
  },
}
</script>

<style scoped>

</style>