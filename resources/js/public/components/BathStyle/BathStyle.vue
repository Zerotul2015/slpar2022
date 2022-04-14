<template>
  <div class="bath-styles-page">
    <div class="bsp-header">
      <div class="bsp-h-top">Комплексный подход к декорированию бань и саун</div>
      <div class="bsp-h-middle">Аксессуары и декор</div>
      <div class="bsp-h-bottom">
        <div>Для бань и саун</div>
        <div>Каминов и печей</div>
      </div>
      <div class="bsp-h-bottom-end">
        <span class="bsp-s-title-icon-toggle" style="margin-right:.5rem"><i class="far fa-chevron-down"></i></span>
        РАЗЛИЧНЫХ ГОТОВЫХ СТИЛЕВЫХ РЕШЕНИЙ
        <span class="bsp-s-title-icon-toggle" style="margin-left:.5rem"><i class="far fa-chevron-down"></i></span>
      </div>
    </div>
    <div class="bsp-sliders">
      <div class="bsp-s-top-line">
        <div class="bsp-s-previous" @click="previousStyle"><i class="far fa-chevron-left"></i></div>
        <div class="bsp-s-current-title" v-html="styleName"></div>
        <div class="bsp-s-next" @click="nextStyle"><i class="far fa-chevron-right"></i></div>
      </div>
      <div class="bsp-s-current-image">
        <img :src="currentStyleImage" :alt="styleName">
      </div>
      <div class="bsp-s-current-description-wrap">
        <div class="bsp-s-current-description-title" @click="toggleDescriptionStyle = !toggleDescriptionStyle">
          <span class="bsp-s-title-icon-toggle" :class="{'bsp-s-title-icon-toggle_up':toggleDescriptionStyle}"><i
              class="far fa-chevron-down"></i></span>
          <span>Особенности этого уникального стиля</span>
          <span class="bsp-s-title-icon-toggle" :class="{'bsp-s-title-icon-toggle_up':toggleDescriptionStyle}"><i
              class="far fa-chevron-down"></i></span>
        </div>
        <div class="bsp-s-current-description-text" v-html="styleDescription"
             v-show="toggleDescriptionStyle"></div>
      </div>

    </div>
    <div class="breadcrumb-wrap"></div>
    <div class="bsp-products">

    </div>
  </div>
</template>

<script>
export default {
  name: "BathStyle",
  props:{
    url:{
      type:String,
      required:false
    }
  },
  data: () => ({
    toggleDescriptionStyle: false,
  }),
  beforeMount() {
    this.$store.dispatch("product/getProduct", 25686);
  },
  computed: {
    product(){
      return this.$store.getters['product/product']
    },
    selectStyleKey(){
      return this.$store.getters['bathStyle/selectKey'];
    },
    bathStyles() {
      return this.$store.getters['bathStyle/all'];
    },
    countStyles() {
      return this.bathStyles.length;
    },
    styleName(){
      if(this.bathStyles[this.selectStyleKey]){
        return this.bathStyles[this.selectStyleKey].name;
      }else{
        return '';
      }
    },
    styleDescription(){
      if(this.bathStyles[this.selectStyleKey]){
        return this.bathStyles[this.selectStyleKey].description;
      }else{
        return '';
      }
    },
    currentStyleImage() {
      if(this.bathStyles[this.selectStyleKey]){
        return '/images/bath-style/' + this.bathStyles[this.selectStyleKey].folder + '/' + this.bathStyles[this.selectStyleKey].image;
      }else{
        return '';
      }
    },
  },
  methods: {
    nextStyle() {
      if (this.selectStyleKey + 1 < this.countStyles) {
        this.$store.dispatch("bathStyle/setActiveStyle", this.selectStyleKey + 1)
      } else {
        this.$store.dispatch("bathStyle/setActiveStyle", 0)
      }
    },
    previousStyle() {
      if (this.selectStyleKey > 0) {
        this.$store.dispatch("bathStyle/setActiveStyle", this.selectStyleKey - 1)
      } else {
        this.$store.dispatch("bathStyle/setActiveStyle", this.countStyles - 1)
      }
    },
  },
}
</script>

<style scoped>
.bsp-s-title-icon-toggle {
  transition: .25s;
}

.bsp-s-title-icon-toggle_up {
  transform: rotate(180deg);
}
</style>