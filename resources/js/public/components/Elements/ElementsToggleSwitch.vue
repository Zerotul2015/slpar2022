<template>
  <div class="toggle-switch">
    <input class="input_toggle_switch" id="toggle_switch" name="toggle_switch" type="checkbox" v-model="isChecked_"/>
    <label class="label_toggle_switch" for="toggle_switch"></label>
  </div>
</template>

<script>
export default {
  name: "ElementsToggleSwitch",
  props: {
    isChecked: {}
  },
  data() {
    return {
      isChecked_: false,
    }
  },
  beforeMount() {
    if (this.isChecked) {
      this.isChecked_ = this.isChecked;
    }
  },
  watch: {
    isChecked_(newVal) {
      newVal = !!newVal;
      this.$emit('changeToggle', newVal);
    }
  }
}
</script>

<style scoped>

.toggle-switch {
  display: grid;
}

.input_toggle_switch {
  width: 0;
  height: 0;
  visibility: hidden;
}


.label_toggle_switch {
  content: "";
  display: block;
  width: 2rem;
  height: 1rem;
  background-color: #adadad;
  border-radius: 100px;
  position: relative;
  cursor: pointer;
  transition: 0.5s;
  box-shadow: 0 0 50px #838383;
}

.label_toggle_switch::after {
  content: "";
  width: .85rem;
  height: .85rem;
  background-color: #4F4F4F;
  position: absolute;
  border-radius: .4rem;
  top: .1rem;
  left: .1rem;
  transition: 0.5s;
}

.input_toggle_switch:checked + .label_toggle_switch {
  background-color: #ffcd03;
}

.input_toggle_switch:checked + .label_toggle_switch:after {
  left: calc(100% - .1rem);
  transform: translateX(-100%);
}
</style>