<template>
  <header class="header-wrap">
    <div class="header-content">
      <a class="header-logo" href="/">
        <img src="/build/images/logo.svg" alt="С легким паром">
      </a>
      <div class="header-bath-style-scroll">
        <bath-styles-header-carousel></bath-styles-header-carousel>
      </div>
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
              {{categoryMain.name}}
            </router-link>
            <div class="h-catalog-child-group" v-if="catalog[categoryMain.id]">
              <router-link class="h-catalog-link-child" v-for="(categoryChild, keyChildCat) in catalog[categoryMain.id]"
                           :key="$root.guid()"
                           :to="'/catalog/' + categoryMain.url + '/' + categoryChild.url">
                <span class="h-icon-link-child"><i class="far fa-chevron-right"></i></span>
                <span class="h-text-link-child">{{ categoryChild.name }}</span>
              </router-link>
            </div>
          </div>
        </div>
      </div>
      <search-site class="header-search-block" :iconShow="true" custom-class="hs-block"></search-site>
      <div class="header-icon-block"></div>
    </div>
  </header>
</template>

<script>
import IconSvg from "./Icon-svg/icon-svg";
import SearchSite from "./search/search-site";
import BathStylesHeaderCarousel from "./BathStyle/BathStylesHeaderCarousel.vue";

export default {
  name: "HeaderApp",
  data() {
    return {
      menuNav: [],
    }
  },
  computed: {
    catalog() {
      return this.$store.getters["templateData/menuCatalog"];
    },
    menuHeader(){
      return this.$store.getters["templateData/menuHeader"];
    },
    bathStyles() {
      return this.$store.getters["bathStyle/all"];
    },
    cart: () => {
      return {
        items: [],
        count: 0,
        sum: 0,
      }; // placeholder
    },
    compare: () => {
      return {
        items: [],
        count: 0,
      }; // placeholder
    }
  },
  components: {BathStylesHeaderCarousel, SearchSite, IconSvg}
}
</script>

<style scoped>

</style>