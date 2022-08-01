<template>
  <div class="style-binding-wrapper">
    <div class="hf-s-title-binding">
      <span>Аксессуары и декор</span>
      <span class="hf-s-binding-toggle-reset" v-show="selectBindingFilterStyleState === true"
            @click="resetBindingCategoryStyle">сбросить</span>
    </div>
    <div class="hf-s-binging-toggle-wrap">
      <div class="hf-s-binging-toggle"
           :class="{'hf-s-binging-toggle_active':selectBindingFilterStyle === 'bath'}"
           @click="changeBindingCategoryStyle('bath')">Для бань и саун
      </div>
      <div class="hf-s-binging-toggle"
           :class="{'hf-s-binging-toggle_active':selectBindingFilterStyle === 'fireplace'}"
           @click="changeBindingCategoryStyle('fireplace')">Для каминов и печей
      </div>
      <div class="hf-s-binging-toggle"
           :class="{'hf-s-binging-toggle_active':selectBindingFilterStyle === 'homestead'}"
           @click="changeBindingCategoryStyle('homestead')">Для дома и усадьбы
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "BathStyleBinding",
  computed:{
    selectBindingFilterStyle() {
      return this.$store.getters['bathStyle/filterBy'];
    },
    selectBindingFilterStyleState() {
      return this.$store.getters['bathStyle/filterToggle'];
    },
  },
  methods:{
    resetBindingCategoryStyle() {
      this.$store.dispatch('bathStyle/disableFilter');
    },
    changeBindingCategoryStyle(bindingName) {
      if (bindingName === 'fireplace' || bindingName === 'bath' || bindingName === 'homestead') {
        if (bindingName === this.selectBindingFilterStyle) {
          this.$store.dispatch('bathStyle/disableFilter');
        } else {
          this.$store.dispatch('bathStyle/setFilter', bindingName);
        }
      }
    }
  }
}
</script>

<style scoped>

</style>