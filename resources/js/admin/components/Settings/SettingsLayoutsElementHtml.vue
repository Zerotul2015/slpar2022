<template>
  <div class="layout-template__line__elements__item layout-template__line__elements__item_html">
    <div class="layout-template__line__elements__item__change">
      <button class="button button_small button_remove layout-template__line__element__remove"
              @click="$emit('remove-element')"><i
          class="far fa-trash-alt"></i> удалить
      </button>
    </div>

    <textarea class="textarea layout-template__line__element__html-raw" v-model="elementLocalVal"></textarea>
    <div class="layout-template__line__element__html-preview" v-html="elementLocalVal"></div>

  </div>
</template>

<script>
export default {
  name: "SettingsLayoutsElementHtml",
  props: {
    elementItem: {
      type: Object,
      required: true
    },

  },
  data: function () {
    return {
      elementLocalVal: this.elementItem.val,
    }
  },
  created: function () {
    if (!this.elementLocalVal) {
      this.elementLocalVal = 'введите или вставьте ваш код'
    }
  },
  watch: {
    elementLocalVal: function (newVal) {
      this.elementItem.val = newVal;
    }
  },
}
</script>

<style scoped>

</style>