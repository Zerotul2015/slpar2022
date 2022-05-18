<template>
  <div class="product-page">
    <div class="pp-wrap" v-if="product.name">
      <h1 class="pp-name">{{ product.name }}</h1>
      <div class="pp-details-wrap">
        <div class="pp-spec-desc">
          <div class="pp-spec-wrap" v-if="product.specifications && product.specifications.length">
            <div class="pp-spec-desc-title">Характеристики:</div>
            <div class="pp-spec-item" v-for="(spec) in product.specifications">
              <div class="pp-spec-item-name">{{ spec.name }}:</div>
              <div class="pp-spec-item-val">{{ spec.val }}</div>
            </div>
          </div>
          <div class="pp-desc-wrap">
            <div class="pp-spec-desc-title">Описание:</div>
            <div class="pp-desc" v-html="product.description"></div>
          </div>
        </div>
        <div class="pp-thumbs-wrap" v-viewer="optionsZoom">
          <img class="pp-image-thumb cursor-pointer" v-for="(imageThumb) in product.images"
               :src="imageDir + '/small/' + imageThumb" :key="$root.guid()"
               :data-source="imageDir + '/1920/' + imageThumb"
               :alt="product.name"
               @click="product.images_main = imageThumb">
        </div>
        <div class="pp-images" v-viewer="optionsZoom">
          <img class="pp-image-main cursor-pointer"  v-if="imageMain" :src="imageDir + '/800/' + imageMain"
               :data-source="imageDir + '/1920/' + imageMain"
               :alt="product.name">
          <img class="pp-image-main"  v-else src="/build/images/noimg.png" :alt="product.name">
        </div>
        <div class="pp-price-act">
          <div class="price-wrap">
            <div class="pp-price">
              <span class="price-to-locale">{{ product.price | priceToLocale }}</span>
              <span class="price-currency">р.</span>
            </div>
            <div class="pp-price-old" v-if="product.price_old && product.price!==product.price_old">
              <span class="price-to-locale">{{ product.price_old | priceToLocale }}</span>
              <span class="price-currency">р.</span>
            </div>
          </div>
          <div class="pp-discounts">
            <div class="pp-discount" v-for="(discountItem) in discountsAsText" v-html="discountItem"></div>
          </div>
          <div class="pp-act">
            <button class="btn btn_compare" :class="{'btn_compare-added':inCompare}" @click.prevent="addCompare">
              <icon-svg class="btn-icon" :icon="compareIcon"></icon-svg>
              <span class="btn-text" v-html="btnCompareText"></span>
            </button>
            <button class="btn btn_green btn_cart" :class="{'btn_cart-added':inCart}" @click.prevent="addCart">
              <icon-svg class="btn-icon" :icon="cartIcon"></icon-svg>
              <span class="btn-text" v-html="btnCartText"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="pp-related-wrap">
      <h2>С этим товаром покупают</h2>
      <vue-horizontal class="bsp-products-wrap" responsive>
        <ProductCard class="product-card_slider" v-for="(productItem) in productsRelated"
                     :product="productItem" :key="$root.guid()"></ProductCard>
      </vue-horizontal>
    </div>
  </div>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";
import VueHorizontal from "vue-horizontal";
import ProductCard from "../Product/ProductCard";
import 'viewerjs/dist/viewer.css'

export default {
  name: "ProductPage",
  components: {VueHorizontal, ProductCard, IconSvg},
  props: {
    url: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      optionsZoom: {
        toolbar: true,
        url: 'data-source',
        zIndex: 9999
      },
    }
  },
  beforeMount() {
    this.$store.dispatch("product/getProductByUrl", this.url);
    this.$store.dispatch("product/getProductsRelated", this.url);
  },
  watch: {
    url(newUrl) {
      this.$store.dispatch("product/getProductByUrl", newUrl);
    }
  },
  computed: {
    discountsAsText() {
      return this.$store.getters['discounts/discountsAsText'];
    },
    productsRelated() {
      return this.$store.getters['product/productsRelated'];
    },
    product() {
      return this.$store.getters['product/product'];
    },
    imageMain() {
      return this.product.image_main;
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
      this.$store.dispatch('cart/addProduct', this.product.id);
    },
  }
}
</script>

<style scoped>
.bsp-products-wrap >>> .v-hl-btn svg {
  width: 40px;
  height: 40px;
  margin: 6px;
  padding: 6px;
  border-radius: 20px;
  background: none;
  color: rgb(60, 171, 61);
  box-shadow: none;
}

.bsp-products-wrap >>> .v-hl-btn svg:hover {
  background: rgb(60, 171, 61);
  color: #ffffff;
}

@media (min-width: 640px) {
  .bsp-products-wrap .product-card_slider {
    width: calc((100% - (2 * 0.5rem)) / 3);
  }
}

@media (min-width: 1024px) {
  .bsp-products-wrap .product-card_slider {
    width: calc((100% - (2 * 0.5rem)) / 3);
  }
}

@media (min-width: 1400px) {
  .bsp-products-wrap .product-card_slider {
    width: calc((100% - (4 * 0.5rem)) / 5);

  }
}

.bsp-products-wrap .product-card_slider {
  margin-right: .55rem;
}
</style>