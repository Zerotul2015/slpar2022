<template>
  <div class="bath-styles-page">
    <div class="bsp-header">
      <div class="bsp-h-top">Комплексный подход к декорированию бань и саун</div>
      <div class="bsp-h-middle">Аксессуары и декор</div>
      <div class="bsp-h-bottom">
        <div>Для бань и саун</div>
        <div>Каминов и печей</div>
      </div>
    </div>
    <div class="bsp-sliders">
      <div class="bsp-s-top-line">
        <div class="bsp-s-previous"></div>
        <div class="bsp-s-current-title"></div>
        <div class="bsp-s-next"></div>
      </div>
      <div class="bsp-s-current-image"></div>
      <div class="bsp-s-current-description"></div>

    </div>
    <div class="breadcrumb-wrap"></div>
    <div class="bsp-broducts"></div>
  </div>
</template>

<script>
export default {
  name: "Home",
  computed: {
    bathStyleKeys(){
    },
    bathStyles() {
      return this.$store.getters['bathStyle/bathStyles'];
    }
  }
}
</script>

<style scoped>

</style>