<template>
  <rl-carousel v-model="slide" no-wrap>
    <div slot-scope="{ wrapperStyles, slides: { count, active} }">
      <div class="overflow-hidden">
        <div v-bind="wrapperStyles">
          <slot>
          </slot>
        </div>
      </div>
      <div class="bs-c-title" v-html="textCarouselTop" @click="disableFilterCategory()">Выбрать стиль</div>
      <div class="bs-c-button bs-c-prev" @click="slide = active - 1; triggerAnimation('Left')"
           :class="{'bs-c-button-disable':slide === 0}">
        <div class="bs-c-button-icon-wrap cursor-pointer">
          <icon-svg class="bs-c-button-icon" :class="{ 'animate-left': animateLeft }" icon="angles-left"></icon-svg>
        </div>
      </div>
      <div class="bs-c-button bs-c-next" @click="slide = active + 1; triggerAnimation('Right')"
           :class="{'bs-c-button-disable':slide === (countStyle-1)}">
        <div class="bs-c-button-icon-wrap cursor-pointer">
          <icon-svg class="bs-c-button-icon" :class="{ 'animate-right': animateRight }" icon="angles-right"></icon-svg>
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
  name: "BathStylesHeaderCarousel",
  components: {IconSvg, RlCarousel, RlCarouselSlide},
  data() {
    return {
      slide: 0,
      animateLeft: true,
      animateRight: true
    }
  },
  beforeMount() {
    this.slide = this.activeStyleKey;
  },
  watch: {
    slide(newKey) {
      this.changeCurrentStyle(newKey);
      if (this.currentSiteSection === 'productCategory') {
        this.$store.dispatch('bathStyle/changeToggleFilterForCategory', true);
      }
    },
    activeStyleKey(newKey) {
      this.slide = newKey;
    },
    currentSiteSection(){
      if(this.currentSiteSection !== 'productCategory'){
        this.disableFilterCategory();
      }
    }
  },
  computed: {
    textCarouselTop() {
      let text = 'выбрать стиль';
      if (this.currentSiteSection === 'productCategory' && this.categoryFilterActive === true) {
        text = 'сбросить выбор';
      }
      return text;
    },
    countStyle() {
      let count = 0;
      if (this.bathStyles) {
        count = this.bathStyles.length;
      }
      return count;
    },
    categoryFilterActive() {
      return this.$store.getters['bathStyle/toggleFilterForCategory'];
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
    disableFilterCategory(){
      if(this.categoryFilterActive === true) {
        this.$store.dispatch('bathStyle/changeToggleFilterForCategory', false);
      }
    },
    triggerAnimation(direction) {
      this[`animate${direction}`] = true
      setTimeout(() => {
        this[`animate${direction}`] = false
      }, 1000)
    },
    nextStyle() {
      console.log(this.$refs.bathSlider[this.activeStyleKey]);

    },
    prevStyle() {
      console.log(this.$refs.bathSlider[this.activeStyleKey]);
    },
    changeCurrentStyle(key) {
      let sectionWhereUsedCarousel = ['index','bathStyle','productCategory','productCategoryWithStyle'];
      if (sectionWhereUsedCarousel.includes(this.currentSiteSection)) {
        this.$store.dispatch("bathStyle/setActiveStyleKey", key);
      }
    }
  },
}
</script>

<style scoped>
.overflow-hidden {
  overflow: hidden;
}

svg {
  transition: color .5s ease;
}

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