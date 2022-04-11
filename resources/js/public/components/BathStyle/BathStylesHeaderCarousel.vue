<template>
  <div class="bs-header-carousel">
    <div><<</div>
    <div>>></div>
  </div>
</template>

<script>
export default {
  name: "BathStylesHeaderCarousel",
  data() {
    return {
      bathStyleActiveKey:null,
    }
  },
  computed:{
    bathStyles(){
      return this.$store.state.bathStyle.bathStylesById;
    }
  }
}
</script>

<style scoped>

</style>