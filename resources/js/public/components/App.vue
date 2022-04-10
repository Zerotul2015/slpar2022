<template>
  <div id="app" class="body-wrap">
    <headerApp :templateData="templateHeaderDate"/>
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
    templateHeaderDate:{},
    templateFooterData:{}
  }),
  created() {

  },
  beforeMount(){
    this.getTemplatesDate();
  },
  computed: {},
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