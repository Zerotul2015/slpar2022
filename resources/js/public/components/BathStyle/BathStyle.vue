<template>
  <div class="bath-styles-page">
    <div class="bsp-header">
      <div class="bsp-h-top">Комплексный подход к декорированию бань и саун</div>
      <div class="bsp-h-middle">Аксессуары и декор</div>
      <div class="bsp-h-bottom-end">
        <icon-svg class="bsp-h-bottom-end-icon" icon="angle-down" color="#fff"></icon-svg>
        <span>РАЗЛИЧНЫХ ГОТОВЫХ СТИЛЕВЫХ РЕШЕНИЙ</span>
        <icon-svg class="bsp-h-bottom-end-icon" icon="angle-down" color="#fff"></icon-svg>
      </div>
      <div class="bsp-h-bottom">
        <div class="bsp-h-toggle-binding" :class="{'bsp-h-toggle-binding_active':selectBindingFilterStyle === 'bath'}"
             @click="changeBindingCategoryStyle('bath')">Для бань и саун
        </div>
        <div class="bsp-h-toggle-binding"
             :class="{'bsp-h-toggle-binding_active':selectBindingFilterStyle === 'fireplace'}"
             @click="changeBindingCategoryStyle('fireplace')">Для каминов и печей
        </div>
        <div class="bsp-h-toggle-binding"
             :class="{'bsp-h-toggle-binding_active':selectBindingFilterStyle === 'homestead'}"
             @click="changeBindingCategoryStyle('fireplace')">Для дома и усадьбы
        </div>
      </div>
    </div>
    <div id="main-sliders-bath-styles" class="bsp-sliders">
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
          <icon-svg class="bsp-s-title-icon-toggle" :class="{'bsp-s-title-icon-toggle_up':toggleDescriptionStyle}"
                    icon="angle-down" color="#fff"></icon-svg>
          <span>Особенности этого уникального стиля</span>
          <icon-svg class="bsp-s-title-icon-toggle" :class="{'bsp-s-title-icon-toggle_up':toggleDescriptionStyle}"
                    icon="angle-down" color="#fff"></icon-svg>
        </div>
        <div class="bsp-s-current-description-text" v-html="styleDescription"
             v-show="toggleDescriptionStyle"></div>
      </div>

    </div>
    <breadcrumb></breadcrumb>
    <div id="products-for-style">
      <div class="bsp-products-group" v-for="(catItem) in productsCategory"
           v-show="filterBindingCategoryState === false || (catItem.binding_style === filterBindingCategory)">
        <h2>{{ catItem.name }}</h2>
        <vue-horizontal class="bsp-products-wrap" responsive>
          <ProductCard class="product-card_slider" v-for="(productItem) in products[catItem.id]"
                       :product="productItem" :key="$root.guid()" image-size="thumb_medium"></ProductCard>
        </vue-horizontal>
      </div>
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
      this.$store.dispatch("bathStyle/setActiveStyleByUrl", this.url);
    }
  },
  watch: {
    activeStyleKey(newKey) {
      //выбираем текущий стил в каруселе стилей в шпаке
      if (this.$refs.menuStyleHeader) {
        this.$refs.menuStyleHeader.scrollToIndex(newKey)
      }
      //меням урл на урл стиля
      if (this.url && this.bathStyles[newKey].url !== this.url) {
        this.$router.push('/bath-style/' + this.bathStyles[this.activeStyleKey].url);
      }
      this.$store.dispatch("bathStyle/getProductsData");
    },
    bathStyles(newVal) {
      //для активации нужно стиля исходя при открытии страницы
      if (this.url) {
        if (newVal[this.activeStyleKey] && newVal[this.activeStyleKey].url !== this.url) {
          this.$store.dispatch("bathStyle/setActiveStyleByUrl", this.url);
        }
      }
    },
    url(newUrl) {
      //смена стиля при смене url
      if (this.bathStyles[this.activeStyleKey] && this.bathStyles[this.activeStyleKey].url !== newUrl) {
        this.$store.dispatch("bathStyle/setActiveStyleByUrl", newUrl);
      }
    },
    filterBindingCategory() {
      let slidersBlock = document.getElementById("main-sliders-bath-styles");
      if (slidersBlock) {
          let coordinate = slidersBlock.getBoundingClientRect();
          window.scrollBy({
            top: coordinate.bottom - 220,
            behavior: 'smooth',
          });
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
    activeStyleKey() {
      return this.$store.getters['bathStyle/activeKey'] ? this.$store.getters['bathStyle/activeKey'] : 0 ;
    },
    bathStyles() {
      return this.$store.getters['bathStyle/all'];
    },
    countStyles() {
      return this.bathStyles.length;
    },
    styleName() {
      if (this.bathStyles[this.activeStyleKey]) {
        return this.bathStyles[this.activeStyleKey].name;
      } else {
        return '';
      }
    },
    styleDescription() {
      if (this.bathStyles[this.activeStyleKey]) {
        return this.bathStyles[this.activeStyleKey].description;
      } else {
        return '';
      }
    },
    currentStyleImage() {
      if (this.bathStyles[this.activeStyleKey]) {
        return '/images/bath-style/' + this.bathStyles[this.activeStyleKey].folder + '/' + this.bathStyles[this.activeStyleKey].image;
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
      if (this.activeStyleKey + 1 < this.countStyles) {
        this.$store.dispatch("bathStyle/setActiveStyleKey", this.activeStyleKey + 1)
      } else {
        this.$store.dispatch("bathStyle/setActiveStyleKey", 0)
      }
    },
    previousStyle() {
      if (this.activeStyleKey > 0) {
        this.$store.dispatch("bathStyle/setActiveStyleKey", this.activeStyleKey - 1)
      } else {
        this.$store.dispatch("bathStyle/setActiveStyleKey", this.countStyles - 1)
      }
    },
    changeBindingCategoryStyle(bindingName) {
      console.log(bindingName);
      if (bindingName === 'fireplace' || bindingName === 'bath' || bindingName === 'homestead') {
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
  color: rgb(255, 205, 3);
  box-shadow: none;
}

.bsp-products-wrap >>> .v-hl-btn svg:hover {
  background: rgb(255, 205, 3);
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