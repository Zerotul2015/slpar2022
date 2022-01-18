<template>
  <div id="app" class="body-wrap">
    <headerApp/>
    <sidebarApp  />
    <main>
      <transition name="component-fade" mode="out-in">
        <router-view></router-view>
      </transition>
    </main>
    <footerApp/>
  </div>
</template>

<script>

import axios from "axios";
import HeaderApp from "./HeaderApp.vue"
import SidebarApp from "./SidebarApp.vue"
import FooterApp from "./FooterApp.vue"

export default {
  name: "app",
  components: {
    HeaderApp,
    SidebarApp,
    FooterApp
  },
  data: () => ({

  }),
  created() {

  },
  computed:{
    authData(){
      return this.$store.state.authData;
    }
  },
  methods: {

  }
}
</script>

<style scoped>
.component-fade-enter-active, .component-fade-leave-active {
  transition: opacity .3s ease;
}
.component-fade-enter, .component-fade-leave-to
  /* .component-fade-leave-active до версии 2.1.8 */ {
  opacity: 0;
}
</style>