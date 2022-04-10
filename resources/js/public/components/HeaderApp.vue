<template>
  <header class="header-wrap">
    <div class="header-content">
      <a class="header-logo" href="/">
        <img src="/build/images/logo.svg" alt="С легким паром">
      </a>
      <div class="header-bath-style-scroll">

      </div>
      <div class="header-links">

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
  props: {
    templateData: {
      type: Object,
      required: true
    },
  },
  data() {
    return {
      menuNav: [],
    }
  },
  computed: {
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