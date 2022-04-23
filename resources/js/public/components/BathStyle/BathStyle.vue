<template>
  <div class="main-content bath-styles-page">
    <div class="bsp-header">
      <div class="bsp-h-top">Комплексный подход к декорированию бань и саун</div>
      <div class="bsp-h-middle">Аксессуары и декор</div>
      <div class="bsp-h-bottom">
        <div class="bsp-h-toggle-binding" :class="{'bsp-h-toggle-binding_active':selectBindingFilterStyle === 'bath'}"
             @click="changeBindingCategoryStyle('bath')">Для бань и саун
        </div>
        <div class="bsp-h-toggle-binding"
             :class="{'bsp-h-toggle-binding_active':selectBindingFilterStyle === 'fireplace'}"
             @click="changeBindingCategoryStyle('fireplace')">Для каминов и печей
        </div>
      </div>
      <div class="bsp-h-bottom-end">
        <icon-svg class="bsp-h-bottom-end-icon" icon="angle-down" color="#fff"></icon-svg>
        <span>РАЗЛИЧНЫХ ГОТОВЫХ СТИЛЕВЫХ РЕШЕНИЙ</span>
        <icon-svg class="bsp-h-bottom-end-icon" icon="angle-down" color="#fff"></icon-svg>
      </div>
    </div>
    <div class="bsp-sliders">
      <div class="bsp-s-top-line">
        <div class="bsp-s-previous" @click="previousStyle">
          <icon-svg class="bsp-s-previous-icon" icon="angle-left" color="#fff"></icon-svg>
        </div>
        <div class="bsp-s-current-title" v-html="'Стиль ' + styleName"></div>
        <div class="bsp-s-next" @click="nextStyle">
          <icon-svg class="bsp-s-next-icon" icon="angle-right" color="#fff"></icon-svg>
        </div>
      </div>
      <div class="bsp-s-current-image">
        <img :src="currentStyleImage" :alt="styleName">
      </div>
      <div class="bsp-s-current-description-wrap">
        <div class="bsp-s-current-description-title" @click="toggleDescriptionStyle = !toggleDescriptionStyle">
          <icon-svg class="bsp-s-title-icon-toggle" :class="{'bsp-s-title-icon-toggle_up':toggleDescriptionStyle}"icon="angle-down" color="#fff"></icon-svg>
          <span>Особенности этого уникального стиля</span>
          <icon-svg class="bsp-s-title-icon-toggle" :class="{'bsp-s-title-icon-toggle_up':toggleDescriptionStyle}"icon="angle-down" color="#fff"></icon-svg>
        </div>
        <div class="bsp-s-current-description-text" v-html="styleDescription"
             v-show="toggleDescriptionStyle"></div>
      </div>

    </div>
    <breadcrumb></breadcrumb>
    <div class="bsp-products-group" v-for="(catItem) in productsCategory"
         v-show="filterBindingCategoryState === false || (catItem.binding_style === filterBindingCategory)">
      <h2>{{ catItem.name }}</h2>
      <vue-horizontal class="bsp-products-wrap" responsive>
        <ProductCard class="product-card_slider" v-for="(productItem) in products[catItem.id]"
                     :product="productItem" :key="$root.guid()" image-size="thumb_medium"></ProductCard>
      </vue-horizontal>
    </div>
  </div>
</template>

<script>
import VueHorizontal from "vue-horizontal";
import iconSvg from "../Icon-svg/icon-svg";
import ProductCard from "../Product/ProductCard";
import Breadcrumb from "../Breadcrumb";

export default {
  name: "BathStyle",
  components: {Breadcrumb, ProductCard, VueHorizontal, iconSvg},
  props: {
    url: {
      type: String,
      required: false
    }
  },
  data: () => ({
    toggleDescriptionStyle: false, //открыто или нет описание стиля(на изображении слайда)
  }),
  beforeMount() {
    if (this.url) {
      this.$store.dispatch("bathStyle/setActiveStyleKeyByUrl", this.url);
    } else {

    }
  },
  watch: {
    selectStyleKey(newKey) {
      //выбираем текущий стил в каруселе стилей в шпаке
      if(this.$refs.menuStyleHeader){
        this.$refs.menuStyleHeader.scrollToIndex(newKey)
      }
      //меням урл на урл стиля
      if (this.url && this.bathStyles[newKey].url !== this.url) {
        this.$router.push('/bath-style/' + this.bathStyles[this.selectStyleKey].url);
      } else {
        this.$store.dispatch("bathStyle/getProductsData");
      }
    },
    bathStyles(newVal) {
      //для активации нужно стиля исходя при открытии страницы
      if (this.url) {
        if (newVal[this.selectStyleKey] && newVal[this.selectStyleKey].url !== this.url) {
          this.$store.dispatch("bathStyle/setActiveStyleKeyByUrl", this.url);
        }
      }
    },
    url(newUrl) {
      //смена стиля при смене url
      if (this.bathStyles[this.selectStyleKey] && this.bathStyles[this.selectStyleKey].url !== newUrl) {
        this.$store.dispatch("bathStyle/setActiveStyleKeyByUrl", newUrl);
      }
    },
  },
  computed: {
    filterBindingCategory() {
      return this.$store.getters['bathStyle/filterBy'];
    },
    filterBindingCategoryState() {
      return this.$store.getters['bathStyle/filterToggle'];
    },
    products() {
      return this.$store.getters['bathStyle/productsData'];
    },
    productsCategory() {
      return this.$store.getters['bathStyle/productsCategoryData'];
    },
    selectStyleKey() {
      return this.$store.getters['bathStyle/selectKey'];
    },
    bathStyles() {
      return this.$store.getters['bathStyle/all'];
    },
    countStyles() {
      return this.bathStyles.length;
    },
    styleName() {
      if (this.bathStyles[this.selectStyleKey]) {
        return this.bathStyles[this.selectStyleKey].name;
      } else {
        return '';
      }
    },
    styleDescription() {
      if (this.bathStyles[this.selectStyleKey]) {
        return this.bathStyles[this.selectStyleKey].description;
      } else {
        return '';
      }
    },
    currentStyleImage() {
      if (this.bathStyles[this.selectStyleKey]) {
        return '/images/bath-style/' + this.bathStyles[this.selectStyleKey].folder + '/' + this.bathStyles[this.selectStyleKey].image;
      } else {
        return '';
      }
    },
    selectBindingFilterStyle() {
      return this.$store.getters['bathStyle/filterBy'];
    },
  },
  methods: {
    nextStyle() {
      if (this.selectStyleKey + 1 < this.countStyles) {
        this.$store.dispatch("bathStyle/setActiveStyleKey", this.selectStyleKey + 1)
      } else {
        this.$store.dispatch("bathStyle/setActiveStyleKey", 0)
      }
    },
    previousStyle() {
      if (this.selectStyleKey > 0) {
        this.$store.dispatch("bathStyle/setActiveStyleKey", this.selectStyleKey - 1)
      } else {
        this.$store.dispatch("bathStyle/setActiveStyleKey", this.countStyles - 1)
      }
    },
    changeBindingCategoryStyle(bindingName) {
      console.log(bindingName);
      if (bindingName === 'fireplace' || bindingName === 'bath') {
        if (bindingName === this.selectBindingFilterStyle) {
          this.$store.dispatch('bathStyle/disableFilter');
        } else {
          this.$store.dispatch('bathStyle/setFilter', bindingName);
        }
      }
    }
  },
}
</script>

<style scoped>
.bsp-s-title-icon-toggle {
  transition: .25s;
}

.bsp-s-title-icon-toggle_up {
  transform: rotate(180deg);
}


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
    width: calc((100% - 0.5rem) / 2);
  }
}

@media (min-width: 1024px) {
  .bsp-products-wrap .product-card_slider {
    width: calc((100% - (2 * 0.5rem)) / 3);
  }
}

@media (min-width: 1280px) {
  .bsp-products-wrap .product-card_slider {
    width: calc((100% - (3 * 0.5rem)) / 4);

  }
}

.bsp-products-wrap .product-card_slider {
  margin-right: .55rem;
}
</style>