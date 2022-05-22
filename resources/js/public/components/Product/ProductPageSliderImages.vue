<template>
  <rl-carousel v-model="slide" no-wrap>
    <div slot-scope="{ wrapperStyles, slides: { count, active} }">
      <div class="overflow-hidden">
        <div v-bind="wrapperStyles">
          <slot>
          </slot>
        </div>
      </div>
      <div class="slider-button slide-button-prev"
           @click="slide = active - 1; triggerAnimation('Left');"
           :class="{'slider-button-disable':slide === 0}">
        <div class="slider-button-icon-wrap cursor-pointer">
          <icon-svg class="slider-button-icon" :class="{ 'animate-left': animateLeft }"
                    icon="angle-left"></icon-svg>
        </div>
      </div>
      <div class="slider-button slide-button-next"
           @click="slide = active + 1; triggerAnimation('Right');"
           :class="{'slider-button-disable':slide === (countImages-1)}">
        <div class="slider-button-icon-wrap cursor-pointer">
          <icon-svg class="slider-button-icon" :class="{ 'animate-right': animateRight }"
                    icon="angle-right"></icon-svg>
        </div>
      </div>
    </div>
  </rl-carousel>
</template>

<script>

import IconSvg from "../Icon-svg/icon-svg";
import {RlCarouselSlide, RlCarousel} from 'vue-renderless-carousel'
import {isNull} from "lodash";

export default {
  name: "ProductPageSliderImages",
  components: {IconSvg, RlCarousel, RlCarouselSlide},
  data() {
    return {
      slide: 0,
      animateLeft: true,
      animateRight: true
    }
  },
  beforeMount() {
    this.slide = this.keyImageMain;
  },
  computed: {
    keyImageMain() {
      return this.$store.getters['product/keyImageMain'];
    },
    countImages(){
      return this.$store.getters['product/countImages'];
    }
  },
  methods:{
    triggerAnimation(direction) {
      this[`animate${direction}`] = true
      setTimeout(() => {
        this[`animate${direction}`] = false
      }, 1000)
    },
  }
}
</script>

<style scoped>

</style>