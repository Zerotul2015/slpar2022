<template>
  <div class="layout-template__line__elements__item layout-template__line__elements__item_page"
       :class="additionClass">
    <div class="input-block layout-template__line__elements__item__change">
      <label>Страница:</label>
      <select class="select" v-model="elementLocalID">
        <option v-for="elementSelectItem in this.pages"
                :value="elementSelectItem.id">{{ elementSelectItem.title }}
        </option>
      </select>
      <br>
      <div class="layout-template__line__element__remove" @click="$emit('remove-element')"><i
          class="far fa-trash-alt"></i></div>
    </div>

    <span v-html="titleText"></span>

  </div>
</template>

<script>
export default {
  name: "SettingsLayoutsElementPage",
  props: {
    elementItem: {
      type: Object,
      required: true
    },

  },
  data: function () {
    return {
      elementLocalID: this.elementItem.id,
    }
  },
  watch: {
    elementLocalID: function (newVal) {
      this.elementItem.id = newVal;
    }
  },
  computed: {
    pages(){
      return this.$store.state.page.allById;
    },
    titleText: function () {
      if (this.pages[this.elementLocalID]) {
        return this.pages[this.elementLocalID].title;
      } else {
        return '<span class="error-description">нужно выбрать страницу</span>';
      }
    },
    additionClass: function () {//layout-template__line__elements__item_page_green
      if (this.pages[this.elementLocalID]) {
        if (this.pages[this.elementLocalID].style) {
          return 'layout-template__line__elements__item_page_' + this.pages[this.elementLocalID].style;
        }
      }
    }
  },
}
</script>

<style scoped>

</style>