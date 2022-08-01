<template>
  <div class="hcs-wrapper">
    <BathStylesCarouselNavigate class="hcd-wrapper">
      <rl-carousel-slide v-for="(styleItem, keyStyle) in bathStyles" :key="$root.guid()">
        <div class="bs-c-item" :class="{'bs-c-item-active':keyStyle === activeStyleKey}">{{
            styleItem.name
          }}
        </div>
      </rl-carousel-slide>
    </BathStylesCarouselNavigate>
  </div>
</template>
<script>
import BathStylesCarouselNavigate from "../BathStyle/BathStylesCarouselNavigate";
import {RlCarouselSlide} from 'vue-renderless-carousel'
export default {
  name: "HeaderStyleCarouselNavigate",
  components: {BathStylesCarouselNavigate, RlCarouselSlide},
  computed: {
    activeStyleKey() {
      return this.$store.getters['bathStyle/activeKey'];
    },
    bathStyles() {
      return this.$store.getters["bathStyle/all"];
    },
  }
}
</script>

<style scoped>

</style>