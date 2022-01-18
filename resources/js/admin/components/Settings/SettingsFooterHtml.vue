<template>
  <div class="template-footer-item input-block" v-html="">
    <textarea class="select" v-model.lazy="contentHtml_"></textarea>
  </div>
</template>

<script>
export default {
  name: "SettingsFooterHtml",
  props: {
    contentHtml: {
      required: {
        type: String,
        required: true,
      }
    },
    colKey:{
      required: true,
    }
  },
  data(){
    return{
      contentHtml_:this.contentHtml,
    }
  },
  watch:{
    contentHtml_(newVal){
      this.changeHtml();
    }
  },
  methods:{
    changeHtml(){
      this.$emit('change-html', this.colKey,this.contentHtml_)
    }
  }
}
</script>

<style scoped>

</style>