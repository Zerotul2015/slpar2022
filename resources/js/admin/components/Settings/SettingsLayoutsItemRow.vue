<template>
  <div class="layout-template__line" :class="{'layout-template__line_vertical':(orientation === 'vertical')}">
    <div class="layout-template__element__add" v-if="orientation === 'horizontal'">
      <div v-if="slotsFree">
        <select class="select" style="width:10rem;" v-model="selectType">
          <option v-for="(typeElementItem, idType) in $parent.$parent.typeElement"
                  :value="idType"
                  v-if="needType === null || usedType == idType">
            {{ typeElementItem }}
          </option>
        </select>
        <br>
        <div style="color:#000; font-size:.7rem; text-align: center" v-html="textAvailableSlots"></div>
      </div>
      <button v-if="slotsFree" class="button button_small ml-1" title="добавить элемент"
              @click="addElement">
        <span><i class="far fa-plus"></i></span>
        <span>добавить элемент</span>
      </button>
      <button class="button button_small button_remove ml-1" @click="$emit('remove-row')"
              title="удалить строку">
        <span><i class="far fa-trash"></i></span>
        <span>удалить строку</span>
      </button>
    </div>
    <div class="layout-template__element__add" v-else="orientation === 'horizontal'">
      <div>
        <select class="select" style="width:10rem;" v-model="selectType">
          <option v-for="(typeElementItem, idType) in $parent.$parent.typeElement"
                  :value="idType">
            {{ typeElementItem }}
          </option>
        </select>
        <br>
      </div>
      <button class="button button_small ml-1" title="добавить элемент"
              @click="addElement">
        <span><i class="far fa-plus"></i></span>
        <span>добавить элемент</span>
      </button>
      <button class="button button_small button_remove ml-1" @click="$emit('remove-row')"
              title="удалить строку">
        <span><i class="far fa-trash"></i></span>
        <span>удалить строку</span>
      </button>
    </div>

    <div class="layout-template__line__elements">
      <SettingsLayoutsElementBanner v-for="(elementItem, indexElement) in elements"
                             v-if="elementItem.type === 1"
                             v-bind:key="'banner' + $root.guid()"
                             v-bind:element-item.sync="elements[indexElement]"
                             v-on:remove-element="elements.splice(indexElement, 1)">
      </SettingsLayoutsElementBanner>
      <SettingsLayoutsElementHtml v-for="(elementItem, indexElement) in elements"
                           v-if="elementItem.type === 3"
                           v-bind:key="'html' + $root.guid()"
                           v-bind:element-item.sync="elements[indexElement]"
                           v-on:remove-element="elements.splice(indexElement, 1)">
      </SettingsLayoutsElementHtml>
      <SettingsLayoutsElementPage v-for="(elementItem, indexElement) in elements"
                           v-if="elementItem.type === 4"
                           v-bind:key="'page' + $root.guid()"
                           v-bind:element-item.sync="elements[indexElement]"
                           v-on:remove-element="elements.splice(indexElement, 1)">
      </SettingsLayoutsElementPage>
    </div>
  </div>
</template>

<script>
import SettingsLayoutsElementBanner from "./SettingsLayoutsElementBanner.vue";
import SettingsLayoutsElementHtml from "./SettingsLayoutsElementHtml.vue";
import SettingsLayoutsElementPage from "./SettingsLayoutsElementPage.vue";
export default {
  name: "SettingsLayoutsItemRow",
  components:{
    SettingsLayoutsElementBanner,
    SettingsLayoutsElementHtml,
    SettingsLayoutsElementPage,
  },
  props: {
    rowElements: {
      type: Array,
      required: true
    },
    orientation: {
      type: String,
      default: 'horizontal'
    }
  },
  data: function () {
    return {
      elements: this.rowElements,
      selectType: 1,
      usedType: null,
    }
  },
  computed: {
    slotsFree: function () {
      let that = this;
      let slotsUsed = 0;
      let elements = this.rowElements;

      elements.forEach(function (elementItem) {
        if (that.$parent.$parent.slotsElementUsed[elementItem.type]) {
          slotsUsed = slotsUsed + that.$parent.$parent.slotsElementUsed[elementItem.type];
        } else {
          slotsUsed = slotsUsed + 1;
        }
      });
      let slotsFree = this.$parent.$parent.slotsAvailable - slotsUsed;
      if (isNaN(slotsFree) || slotsFree < 0) {
        slotsFree = 0
      }
      return slotsFree;
    },
    selectTypeMaxCount: function () {
      let usedSlots = 1;
      if (this.$parent.$parent.slotsElementUsed[this.selectType]) {
        usedSlots = this.$parent.$parent.slotsElementUsed[this.selectType];
      }
      let maxElementCount = this.slotsFree / usedSlots;
      maxElementCount = Math.trunc(maxElementCount);
      return maxElementCount;
    },
    textAvailableSlots: function () {
      let textReturn = 'Нет места для данного элемента';
      if (this.selectTypeMaxCount) {
        textReturn = 'Можно добавить еще: ' + this.selectTypeMaxCount + ' шт.'
      }
      return textReturn;
    },
    needType: function () {
      if (this.usedType === null) {
        return null;
      } else {
        return this.usedType;
      }
    }
  },
  watch: {
    rowElements: function (newVal) {
      let that = this;
      let usedTypeInRow = 1;
      if (newVal.length) {
        newVal.forEach(function (elementItem) {
          usedTypeInRow = elementItem.type;
        });
      } else {
        usedTypeInRow = null;
      }
      that.usedType = usedTypeInRow;
    },
    selectType: function (newVal) {
      let parseVal = parseInt(newVal);
      if (isNaN(parseVal)) {
        this.selectType = 1;
      } else {
        this.selectType = parseVal;
      }
    },
    usedType: function () {

    }
  },
  methods: {
    addElement: function () {
      if (this.orientation === 'vertical') {
        if (this.$parent.$parent.typeElement[this.selectType]) {
          this.rowElements.push({'type': this.selectType, 'id': null})
        }
      } else {
        if (this.selectTypeMaxCount) {
          if (this.$parent.$parent.typeElement[this.selectType]) {
            this.rowElements.push({'type': this.selectType, 'id': null})
          }
        }
      }
    },
  },
}
</script>

<style scoped>

</style>