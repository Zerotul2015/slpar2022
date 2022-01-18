<template>
  <div class="layout-template__line__elements__item layout-template__line__elements__item_banner">
    <div class="input-block layout-template__line__elements__item__change">
      <label>Баннер:</label>
      <select class="select" v-model="elementLocalID">
        <option v-for="elementSelectItem in this.banners"
                :value="elementSelectItem.id">{{ elementSelectItem.banner_name }}
        </option>
      </select>
      <br>
      <div class="layout-template__line__element__remove" @click="$emit('remove-element')"><i
          class="far fa-trash-alt"></i></div>
    </div>
    <img class="layout-template__line__element__item__image" :src="imageBanner" alt="">
  </div>
</template>

<script>
export default {
  name: "SettingsLayoutsElementBanner",
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
    banners(){
      return this.$store.state.banners.allById;
    },
    imageBanner: function () {
      if (this.banners[this.elementLocalID]) {
        return '/images/banner/header_background/' + this.banners[this.elementLocalID].image;
      } else {
        return '/build/images/admin/layouts/placeholder-image.jpg';
      }
    },
  },
}
</script>

<style scoped>

</style>