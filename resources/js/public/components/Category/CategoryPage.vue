<template>
  <div>
    страница каталога
  </div>
</template>

<script>
export default {
  name: "CategoryPage",
  props: {
    url: {
      required: true,
      type: String,
    }
  },
  beforeMount() {
    this.changeCategory();
  },
  watch: {
    url() {
      this.changeCategory();
    }
  },
  methods:{
    changeCategory(){
      this.$store.commit("templateData/setSection", 'productCategory');
      this.$store.commit("templateData/setSectionKey", this.url);
      this.$store.dispatch("templateData/getChange");
    }
  }
}
</script>

<style scoped>

</style>