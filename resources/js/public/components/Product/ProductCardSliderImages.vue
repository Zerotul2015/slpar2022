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
           @click.prevent="slide = active - 1; triggerAnimation('Left');"
           :class="{'slider-button-disable':(slide === 0 || count === 1)}">
        <div class="slider-button-icon-wrap cursor-pointer">
          <icon-svg class="slider-button-icon" :class="{ 'animate-left': animateLeft }"
                    icon="angle-left"></icon-svg>
        </div>
      </div>
      <div class="slider-button slide-button-next"
           @click.prevent="slide = active + 1; triggerAnimation('Right');"
           :class="{'slider-button-disable':(slide === (countImages-1)|| count === 1)}">
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

export default {
  name: "ProductCardSliderImages",
  components: {IconSvg, RlCarousel, RlCarouselSlide},
  props:{
    keyImageMain:{
      default:0
    },
    countImages:{
      default:0,
    }
  },
  data() {
    return {
      slide: this.keyImageMain,
      animateLeft: true,
      animateRight: true
    }
  },
  beforeMount() {

  },
  computed: {

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
.product-card .slider-button{
  display:none;

}
.product-card:hover .slider-button{
  display:grid;
}
.product-card:hover .slider-button-disable{
  display:none;
}
</style>