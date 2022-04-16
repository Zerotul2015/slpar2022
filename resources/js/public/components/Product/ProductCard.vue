<template>
  <div class="product-card">
    <div class="pc-name">{{product.name}}</div>
    <img class="pc-image-main" :src="imageMain">
    <div class="pc-price-wrap" v-if="product.price_on_request">
      Цена по запросу
    </div>
    <div class="pc-price-wrap" v-else>
      <div class="pc-price">
        <span class="price-to-locale">{{ product.price | priceToLocale }}</span>
        <span class="price-currency">р.</span>
      </div>
      <div class="pc-price-old" v-if="product.price_old && product.price!==product.price_old">
        <span class="price-to-locale">{{ product.price_old | priceToLocale }}</span>
        <span class="price-currency">р.</span>
      </div>
    </div>
    <div class="pc-buttons">
      <button class="btn" v-html="btnFavoriteText"></button>
      <button class="btn btn_green" v-html="btnCartText"></button>
    </div>
    <div class="pc-favorite">
      <icon-svg icon="svg-favorite"></icon-svg>
    </div>
  </div>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";
export default {
  name: "ProductCard",
  components: {IconSvg},
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  computed:{
    btnFavoriteText(){
      let text = '<span class="btn-text">нравится</span>'
      let icon = '';
      return icon + text;
    },
    btnCartText(){
      let text = '<span class="btn-text">в корзине</span>'
      let icon = '';
      return icon + text;
    },
    imageMain(){
      let image = '/build/images/noimg.png/';
      if(this.product.image_main){
        image = '/images/products/' + this.product.folder + '/thumb/' + this.product.image_main
      }
      return image;
    }
  }
}
</script>

<style scoped>

</style>