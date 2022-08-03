<template>
  <div id="app" class="body-wrap" :class="{'app_mobile':isMobile, 'app_tablet':isTablet}">
    <headerApp/>
    <div class="content-wrap" :class="contentWrapClassName">
      <breadcrumb v-if="showBreadcrumb"></breadcrumb>
      <main>
        <transition name="component-fade" mode="out-in">
          <router-view></router-view>
        </transition>
      </main>
    </div>
    <footerApp/>
  </div>
</template>

<script>

import axios from "axios";
import HeaderApp from "./HeaderApp.vue"
import Breadcrumb from "./Breadcrumb.vue";
import FooterApp from "./FooterApp.vue"
import {kebabCase} from "lodash"

export default {
  name: "app",
  components: {
    HeaderApp,
    Breadcrumb,
    FooterApp
  },
  data: () => ({}),
  created() {

  },
  beforeMount() {
    this.$store.dispatch('templateData/getTemplateSettings');
    this.$store.dispatch('bathStyle/getAll');
    this.$store.dispatch('cart/getCart');
    this.$store.dispatch('compare/getCompare');
    this.$store.dispatch('discounts/getDiscounts');
    this.$store.dispatch('customer/checkAuth');
  },
  watch: {
    seo(newVal, oldVal) {
      document.title = newVal.title;
      document.description = newVal.description;
    },
    section() {
      this.$store.dispatch('templateData/getChange');
    },
    sectionKey() {
      this.$store.dispatch('templateData/getChange');
    },
    styleHeaderToggle(newVal, oldVal){
      if(newVal === false && oldVal === true){
        this.$store.dispatch('bathStyle/resetFilterForCategory');
      }
    }
  },
  computed: {
    contentWrapClassName(){
      return 'content-wrap-' + kebabCase(this.section);
    },
    isMobile() {
      return this.$store.getters['templateData/isMobile'];
    },
    isTablet() {
      return this.$store.getters['templateData/isTablet'];
    },
    showBreadcrumb() {
      let disallowSection = ['bathStyle', 'home'];
      return !disallowSection.includes(this.section);
    },
    seo() {
      return this.$store.getters["templateData/seo"];
    },
    section() {
      return this.$store.getters["templateData/section"];
    },
    sectionKey() {
      return this.$store.getters["templateData/sectionKey"];
    },
    isWholesale() {
      return this.$store.getters['customer/isWholesale'];
    },
    styleHeaderToggle(){
      return this.$store.getters['bathStyle/styleHeaderToggle'];
    }
  },
  methods: {}
}
</script>

<style scoped>
.component-fade-enter-active, .component-fade-leave-active {
  transition: opacity .3s ease;
}

.component-fade-enter, .component-fade-leave-to
  /* .component-fade-leave-active до версии 2.1.8 */
{
  opacity: 0;
}
</style>