<template>
  <div class="bs-header-carousel">
    <div class="bs-c-button bs-c-prev" @click="prevStyle()">
      <icon-svg class="bs-c-button-icon" icon="angles-left"></icon-svg>
    </div>
    <div class="bs-c-button bs-c-next" @click="nextStyle()">
      <icon-svg class="bs-c-button-icon" icon="angles-right"></icon-svg>
    </div>
    <div class="bs-c-items" :style="{'width':widthSlider}">
      <div class="bs-c-item" v-for="(bathItem,keyBath) in bathStyles" :key="$root.guid()" ref="bathSlider">
        {{ bathItem.name }}
      </div>
    </div>
  </div>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";

export default {
  name: "BathStylesHeaderCarousel",
  components: {IconSvg},
  data() {
    return {
      widthContainer: 0,
      widthCurrentStyle: 0,
      marginToNext: 0,
      marginToPrev: 0,
    }
  },
  mounted() {
    this.calcWidthSliders();
    this.calcWidthContainer();
  },
  watch: {
    widthCurrentStyle(newWidth) {
      console.log('widthCurrentStyle ' + newWidth);
    },
    widthContainer(newWidth) {
      console.log('widthContainer ' + newWidth);
    },
    marginToPrev() {
    },
    marginToNext() {
    },
    activeStyleKey(newKey) {
      this.calcWidthSliders();
    }
  },
  computed: {
    countStyle() {
      let count = 0;
      if (this.bathStyles) {
        count = this.bathStyles.length;
      }
      return count;
    },
    widthSlider() {
      return 'calc(' + this.widthCurrentStyle + 'px + ' + '6rem)';
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
    nextStyle() {
      console.log(this.$refs.bathSlider[this.activeStyleKey]);

    },
    prevStyle() {
      console.log(this.$refs.bathSlider[this.activeStyleKey]);

    },
    calcWidthContainer() {
      console.log('начало calcWidthContainer');
      let widthContainer = 0;
      if (this.bathStyles[this.activeStyleKey]) {
        if (this.$refs.bathSlider[this.activeStyleKey]) {
          this.$refs.bathSlider.forEach(slide => {
            console.log(slide);
            widthContainer = widthContainer + slide.clientWidth;
          })
        }
      }
      this.widthContainer = widthContainer;
    },
    calcWidthSliders() {
      console.log('начало calcWidthSliders');
      if (this.bathStyles[this.activeStyleKey]) {
        if (this.$refs.bathSlider[this.activeStyleKey]) {
          this.widthCurrentStyle = this.$refs.bathSlider[this.activeStyleKey].clientWidth;
        }
      }
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

</style>