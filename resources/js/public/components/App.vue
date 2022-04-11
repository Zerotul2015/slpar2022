<template>
  <div id="app" class="body-wrap">
    <headerApp/>
    <div class="body-wrap">
      <main class="">
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
import FooterApp from "./FooterApp.vue"

export default {
  name: "app",
  components: {
    HeaderApp,
    FooterApp
  },
  data: () => ({
  }),
  created() {

  },
  beforeMount(){
    this.$store.dispatch('templateData/getTemplateSettings');
  },
  watch:{
    templateSeo(newVal, oldVal){
      document.title = newVal.title;
      document.description = newVal.description;
    }
  },
  computed: {
    templateSeo(){
      return this.$store.getters["templateData/seo"];
    }
  },
  methods: {
    getTemplatesDate(){

    }
  }
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