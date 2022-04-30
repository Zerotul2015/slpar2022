<template>
  <header class="header-page" :class="{'header-page_fixed':headerFixed}">
    <div class="header-wrap">
      <div class="header-content">
        <a class="header-logo" href="/">
          <img src="/build/images/logo.svg" alt="С легким паром">
        </a>
        <div class="header-catalog">
          <div class="h-catalog-link h-link">Каталог<span class="h-catalog-link-caret"><i
              class="fas fa-caret-up"></i></span></div>
          <div class="h-catalog">
            <div class="h-catalog-group">
              <router-link class="h-catalog-link-main" :to="'/bath-style/'">Комплексные стилевые решения</router-link>
              <div class="h-catalog-child-group" v-if="bathStyles">
                <router-link class="h-catalog-link-child" v-for="(styleItem, keyStyle) in bathStyles"
                             :key="$root.guid()"
                             :to="'/bath-style/' + styleItem.url">
                  <span class="h-icon-link-child"><i class="far fa-chevron-right"></i></span>
                  <span class="h-text-link-child">{{ styleItem.name }}</span>
                </router-link>
              </div>
            </div>
            <div class="h-catalog-group" v-for="(categoryMain, keyGroup) in catalog[0]">
              <router-link class="h-catalog-link-main" :to="'/catalog/'+ categoryMain.url">
                {{ categoryMain.name }}
              </router-link>
              <div class="h-catalog-child-group" v-if="catalog[categoryMain.id]">
                <router-link class="h-catalog-link-child"
                             v-for="(categoryChild, keyChildCat) in catalog[categoryMain.id]"
                             :key="$root.guid()"
                             :to="'/catalog/' + categoryMain.url + '/' + categoryChild.url">
                  <span class="h-icon-link-child"><i class="far fa-chevron-right"></i></span>
                  <span class="h-text-link-child">{{ categoryChild.name }}</span>
                </router-link>
              </div>
            </div>
          </div>
        </div>
        <div class="header-links-block">
          <BathStylesHeaderCarousel v-if="headerFixed"></BathStylesHeaderCarousel>
          <div class="hlb-links" v-else>
            <router-link class="h-link" v-for="(menuItem) in menuHeader"
                         :key="$root.guid()"
                         :to="{path:menuItem.value, params:{isCustom:(menuItem.typeItem === 'custom')}}">{{ menuItem.title }}
            </router-link>
          </div>
        </div>
        <search-site class="header-search-block" :iconShow="true" custom-class="hs-block"></search-site>
        <div class="header-icon-block">
          <router-link to="/compare" div class="header-icon-compare-wrap">
            <icon-svg class="header-icon-compare" :class="{'header-icon-compare-full':compareCount}"
                      :scaleX="2" :icon="compareIcon"></icon-svg>
          </router-link>
          <router-link to="/cart" class="header-icon-cart-wrap" :class="{'header-icon-label-full':cartCount}">
            <icon-svg class="header-icon-cart" icon="cart-header"></icon-svg>
          </router-link>
        </div>
      </div>
    </div>
    <div v-if="headerFixed && (sectionSite==='index' || sectionSite ==='bathStyle')" class="h-fixed-second-line">
      <div class="hf-s-title-binding">
        <span>Аксессуары и декор</span>
        <span class="hf-s-binding-toggle-reset" v-show="selectBindingFilterStyleState === true"
              @click="resetBindingCategoryStyle">сбросить</span>
      </div>
      <div class="hf-s-binging-toggle-wrap">
        <div class="hf-s-binging-toggle" :class="{'hf-s-binging-toggle_active':selectBindingFilterStyle === 'bath'}"
             @click="changeBindingCategoryStyle('bath')">Для бань и саун
        </div>
        <div class="hf-s-binging-toggle"
             :class="{'hf-s-binging-toggle_active':selectBindingFilterStyle === 'fireplace'}"
             @click="changeBindingCategoryStyle('fireplace')">Для каминов и печей
        </div>
        <div class="hf-s-binging-toggle"
             :class="{'hf-s-binging-toggle_active':selectBindingFilterStyle === 'homestead'}"
             @click="changeBindingCategoryStyle('homestead')">Для дома и усадьбы
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import VueHorizontal from "vue-horizontal";
import IconSvg from "./Icon-svg/icon-svg";
import SearchSite from "./search/search-site";
import BathStylesHeaderCarousel from "./BathStyle/BathStylesHeaderCarousel.vue";


export default {
  name: "HeaderApp",
  components: {BathStylesHeaderCarousel, SearchSite, IconSvg, VueHorizontal},
  data() {
    return {
      menuNav: [],
    }
  },
  watch: {
    selectBathStyleIndex(newVal) {
      if (newVal === this.$refs) {

      }
    }
  },
  computed: {
    sectionSite() {
      return this.$store.getters["templateData/section"];
    },
    headerFixed() {
      return this.$root.scrollY > 0;
    },
    catalog() {
      return this.$store.getters["templateData/menuCatalog"];
    },
    menuHeader() {
      return this.$store.getters["templateData/menuHeader"];
    },
    bathStyles() {
      return this.$store.getters["bathStyle/all"];
    },
    selectBathStyleIndex() {
      return this.$store.getters['bathStyle/selectKey'];
    },
    selectBindingFilterStyle() {
      return this.$store.getters['bathStyle/filterBy'];
    },
    selectBindingFilterStyleState() {
      return this.$store.getters['bathStyle/filterToggle'];
    },
    cartCount() {
      return this.$store.getters['cart/count'];
    },
    compareCount() {
      return this.$store.getters['compare/count'];
    },
    compareIcon() {
      let iconName = 'compare-empty';
      if (this.compareCount) {
        iconName = 'compare-full';
      }
      return iconName;
    }
  },
  methods: {
    resetBindingCategoryStyle() {
      this.$store.dispatch('bathStyle/disableFilter');
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
.hlb-menu-style >>> .v-hl-btn-prev {
  background: linear-gradient(to left, #4f4f4f00 0, #4f4f4f 66%, #4f4f4f);
  padding-right: 1rem;
  padding-left: 3rem;
}

.hlb-menu-style >>> .v-hl-btn-next {
  background: linear-gradient(to right, #4f4f4f00 0, #4f4f4f 66%, #4f4f4f);
  padding-left: 1rem;
  padding-right: 3rem;
}
</style>