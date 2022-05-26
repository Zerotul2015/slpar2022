<template>
  <rl-carousel v-model="slide" no-wrap :align="'left'">
    <div slot-scope="{ wrapperStyles, slides: { count, active} }">
      <div class="overflow-hidden">
        <div v-bind="wrapperStyles">
          <slot>
          </slot>
        </div>
      </div>
      <div class="slider-button slider-button_medium slide-button-prev"
           @click.prevent="slide = active - 1; triggerAnimation('Left');"
           :class="{'slider-button-disable':(slide === 0 || count === 1)}">
        <div class="slider-button-icon-wrap cursor-pointer">
          <icon-svg class="slider-button-icon" :class="{ 'animate-left': animateLeft }"
                    icon="angle-left" scale-y="2" scale-x="2"></icon-svg>
        </div>
      </div>
      <div class="slider-button slider-button_medium slide-button-next"
           @click.prevent="slide = active + 1; triggerAnimation('Right');"
           :class="{'slider-button-disable':(slide === (countSlide-1)|| count === 1)}">
        <div class="slider-button-icon-wrap cursor-pointer">
          <icon-svg class="slider-button-icon" :class="{ 'animate-right': animateRight }"
                    icon="angle-right" scale-y="2" scale-x="2"></icon-svg>
        </div>
      </div>
    </div>
  </rl-carousel>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";
import {RlCarousel} from 'vue-renderless-carousel'

export default {
  name: "BathStyleSliderProducts",
  components: {IconSvg, RlCarousel},
  props:{
    keySlideStart:{
      default:0
    },
    countSlide:{
      default:0,
    }
  },
  data() {
    return {
      slide: this.keySlideStart,
      animateLeft: true,
      animateRight: true
    }
  },
  beforeMount() {

  },
  computed: {},
  methods: {
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