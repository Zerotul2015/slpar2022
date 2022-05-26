<template>
  <bath-style-slider-products class="products-group" v-if="products[683] && products[683].length > 0" :count-slide="products[683].length">
    <rl-carousel-slide v-for="(productItem) in products[683]" :key="$root.guid()" >
      <ProductCard class="product-card_slider"
                   :product="productItem" :key="$root.guid()" image-size="thumb_medium"></ProductCard>
    </rl-carousel-slide>
  </bath-style-slider-products>
</template>

<script>
import {RlCarouselSlide} from 'vue-renderless-carousel'
import BathStyleSliderProducts from "../BathStyle/BathStyleSliderProducts";
export default {
  name: "Test",
  beforeMount() {
    this.$store.dispatch("bathStyle/setActiveStyleKey", 2);
    this.$store.dispatch("bathStyle/getProductsData");
  },
  computed:{
    products(){
      return this.$store.getters['bathStyle/productsData'];
    }
  }
}
</script>

<style scoped>

</style>