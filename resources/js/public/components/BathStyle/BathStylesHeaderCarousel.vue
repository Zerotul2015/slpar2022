<template>
  <rl-carousel v-model="slide" no-wrap>
    <div slot-scope="{ wrapperStyles, slides: { count, active} }">
      <div class="overflow-hidden">
        <div v-bind="wrapperStyles">
          <slot>
          </slot>
        </div>
      </div>
      <div class="bs-c-button bs-c-prev" @click="slide = active - 1; triggerAnimation('Left')">
        <div class="group flex-grow flex items-center justify-center cursor-pointer">
          <span class="control-chevron" :class="{ 'animate-left': animateLeft }">
            <icon-svg class="bs-c-button-icon" icon="angles-left" style="fill:#ffffff"></icon-svg>
          </span>
        </div>
      </div>
      <div class="bs-c-button bs-c-next" @click="slide = active + 1; triggerAnimation('Right')">
        <div class="group flex-grow flex items-center justify-center cursor-pointer">
          <span class="control-chevron" :class="{ 'animate-right': animateRight }">
            <icon-svg class="bs-c-button-icon" icon="angles-right" style="fill:#ffffff"></icon-svg>
          </span>
        </div>
      </div>
    </div>
  </rl-carousel>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";
import { RlCarouselSlide, RlCarousel } from 'vue-renderless-carousel'

export default {
  name: "BathStylesHeaderCarousel",
  components: {IconSvg, RlCarousel, RlCarouselSlide},
  data() {
    return {
      slide: 0,
      animateLeft: true,
      animateRight: true
    }
  },
  mounted() {

  },
  watch: {

  },
  computed: {
    countStyle() {
      let count = 0;
      if (this.bathStyles) {
        count = this.bathStyles.length;
      }
      return count;
    },

    bathStyleCarousel() {

    },
    bathStyles() {
      return this.$store.getters['bathStyle/all']
    },
    activeStyleKey() {
      return this.$store.getters['bathStyle/activeKey'];
    },
    activeStyleId() {
      return this.$store.getters['bathStyle/activeId'];
    },
    currentSiteSection() {
      return this.$store.getters['templateData/section'];
    },

  },
  methods: {
    triggerAnimation (direction) {
      this[`animate${direction}`] = true
      setTimeout(() => { this[`animate${direction}`] = false }, 1000)
    },
    nextStyle() {
      console.log(this.$refs.bathSlider[this.activeStyleKey]);

    },
    prevStyle() {
      console.log(this.$refs.bathSlider[this.activeStyleKey]);

    },
    changeCurrentStyle(id) {
      if (this.currentSiteSection === 'bathStyle' || this.currentSiteSection === 'index') {
        this.$store.dispatch("bathStyle/setActiveStyleKeyById", id);
      }
    }
  },
}
</script>

<style scoped>
.overflow-hidden{
  overflow: hidden;
}

svg { transition: color .5s ease; }

.control-chevron {
  transition: font-size .5s ease;
}
.control-chevron.animate-left {
    animation: animate-left 1s;
  }
.control-chevron.animate-right {
    animation: animate-right 1s;
  }

@keyframes animate-left {
  50% {
    transform: translateX(-10px)
  }
}
@keyframes animate-right {
  50% {
    transform: translateX(10px)
  }
}
</style>