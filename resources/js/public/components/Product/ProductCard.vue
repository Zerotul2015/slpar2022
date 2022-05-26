<template>
  <router-link :to="'/product/' + product.url" class="product-card" :class="'product-card_'+imageSize">
    <!--    <img class="pc-image-main" :src="imageMain">-->
    <div class="pc-image-main" :class="'pc-image-main_'+imageSize" v-if="product.image_main">
      <product-card-slider-images :key-image-main="keyImageMain" :count-images="countImages" :key="$root.guid()">
        <rl-carousel-slide v-for="(imageItem, keyStyle) in product.images" :key="$root.guid()">
          <img class="cursor-pointer" :src="imageDir + '/' + imageSize + '/' + imageItem" :alt="product.name">
        </rl-carousel-slide>
      </product-card-slider-images>
    </div>
    <img v-else class="pc-image-main" :class="'pc-image-main_'+imageSize" :src="imageMain">
    <div class="pc-name">{{ product.name }}</div>
    <div class="pc-description">{{ descriptionText }}</div>
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
      <button class="btn btn_compare" :class="{'btn_compare-added':inCompare}" @click.prevent="addCompare">
        <icon-svg class="btn-icon" :icon="compareIcon"></icon-svg>
        <span class="btn-text" v-html="btnCompareText"></span>
      </button>
      <button class="btn btn_green btn_cart" :class="{'btn_cart-added':inCart}" @click.prevent="addCart">
        <icon-svg class="btn-icon" :icon="cartIcon"></icon-svg>
        <span class="btn-text" v-html="btnCartText"></span>
      </button>
    </div>
  </router-link>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";
import ProductCardSliderImages from "./ProductCardSliderImages";
import {RlCarouselSlide} from 'vue-renderless-carousel'
import {isNull} from "lodash";

export default {
  name: "ProductCard",
  components: {ProductCardSliderImages, IconSvg, RlCarouselSlide},
  props: {
    product: {
      type: Object,
      required: true
    },
    imageSize: {
      type: String,
      required: false,
      default:'thumb'
    },
  },
  data() {
    return {}
  },
  watch: {},
  computed: {
    keyImageMain() {
      let indexImage = 0;
      if (this.product.images) {
        indexImage = this.product.images.findIndex(item => item === this.product.image_main);
        if (indexImage === -1) {
          indexImage = null;
        }
      }
      return indexImage;
    },
    countImages() {
      return this.product.images ? this.product.images.length : 0;
    },
    imageDir() {
      return '/images/products/' + this.product.folder + '';
    },
    inCart() {
      return !!this.$store.getters['cart/products'][this.product.id];
    },
    cartIcon() {
      return this.inCart ? 'cart-circle-check' : 'cart-circle-plus';
    },
    btnCartText() {
      return this.inCart ? 'в корзине' : 'в корзину';
    },
    inCompare() {
      return !!this.$store.getters['compare/products'][this.product.id];
    },
    compareIcon() {
      return this.inCompare ? 'square-check' : '';
    },
    btnCompareText() {
      return this.inCompare ? 'в сравнение' : 'в сравнение';
    },
    descriptionText() {
      let text = this.product.description ? this.product.description.replace(/<\/?[a-zA-Z]+>/gi, '') : '';
      if (text.length > 130) {
        text = text.slice(0, 128).replace(/\s\S+$/g, '') + '...';
      }
      return text;
    },
    imageMain() {
      let image = '/build/images/noimg.png';
      let availableImageSize = {'thumb': true, 'thumb_medium': true}
      let imageSize = (this.imageSize && availableImageSize[this.imageSize]) ? this.imageSize : 'thumb';
      if (this.product.image_main) {
        image = '/images/products/' + this.product.folder + '/' + imageSize + '/' + this.product.image_main
      }
      return image;
    }
  },
  methods: {
    addCompare() {
      if (this.inCompare) {
        this.$store.dispatch('compare/removeProduct', this.product.id);
      } else {
        this.$store.dispatch('compare/addProduct', this.product.id);
      }
    },
    addCart() {
      if (this.inCart) {
        this.$router.push('/Cart');
      } else {
        this.$store.dispatch('cart/addProduct', this.product.id);
      }
    },
  }
}
</script>

<style scoped>

</style>