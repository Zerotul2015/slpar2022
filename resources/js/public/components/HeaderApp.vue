<template>
  <header class="header-wrap">
    <div class="header-content">
      <a class="header-logo" href="/">
        <img src="/build/images/logo.svg" alt="С легким паром">
      </a>
      <div class="header-bath-style-scroll">

      </div>
      <div class="header-links">
        <div class="h-link">Каталог</div>
        <div class="h-catalog">
          <div class="h-catalog-group" v-for="(categoryMain, keyGroup) in catalog[0]">
            <a class="h-catalog-link-main" :href="'/catalog/' + categoryMain.url">{{categoryMain.name}}</a>
            <div class="h-catalog-child-group" v-if="catalog[0]">
              <a class="h-catalog-link-child" v-for="(categoryChild, keyChildCat) in catalog[0]"
                 :href="'/catalog/' + categoryMain.url + '/' + categoryChild.url">
                <span class="h-icon-link-child"><i class="far fa-chevron-right"></i></span>
                <span class="h-text-link-child">{{categoryChild.name}}</span>
              </a>
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
import SearchAutoComplete from "../components/search/search-auto-complete.vue";
import SearchSite from "./search/search-site";

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
  components: {SearchSite, SearchAutoComplete, IconSvg}
}
</script>

<style scoped>

</style>