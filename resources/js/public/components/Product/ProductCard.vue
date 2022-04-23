<template>
  <router-link :to="'/product/' + product.url" class="product-card">
    <div class="pc-name">{{ product.name }}</div>
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
      <button class="btn btn_favorite"  :class="{'btn_favorite-added':inFavorite}" v-html="btnFavoriteText" @click.prevent="addFavorite"></button>
      <button class="btn btn_green btn_cart" :class="{'btn_cart-added':inCart}"  @click.prevent="addCart">
        <icon-svg class="btn-icon" :icon="cartIcon"></icon-svg>
        <span class="btn-text" v-html="btnCartText"></span>
      </button>
    </div>
  </router-link>
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
    },
    imageSize: {
      type: String,
      required: false
    },
  },
  computed: {
    cartIcon(){
      return this.inCart ? 'cart-shopping' : 'cart-plus';
    },
    inCart() {
      return !!this.$store.getters['cart/products'][this.product.id];
    },
    inFavorite() {
      return !!this.$store.getters['favorite/products'][this.product.id];
    },
    btnFavoriteText() {
      let text = this.inFavorite ? 'в закладках' : 'в закладки';
      text = '<span class="btn-text">' + text + '</span>';
      let icon = '';
      return icon + text;
    },
    btnCartText() {
      let text = this.inCart ? 'в корзине' : 'в корзину';
      text = '<span class="btn-text">' + text + '</span>';
      let icon = '';
      return icon + text;
    },
    imageMain() {
      let image = '/build/images/noimg.png/';
      let availableImageSize = {'thumb': true, 'thumb_medium': true}
      let imageSize = (this.imageSize && availableImageSize[this.imageSize]) ? this.imageSize : 'thumb';
      if (this.product.image_main) {
        image = '/images/products/' + this.product.folder + '/' + imageSize + '/' + this.product.image_main
      }
      return image;
    }
  },
  methods: {
    addFavorite() {
      this.$store.dispatch('favorite/addProduct', this.product.id);
    },
    addCart() {
      this.$store.dispatch('cart/addProduct', this.product.id);
    },
  }
}
</script>

<style scoped>

</style>