<template>
    <div class="page">
      <h1 v-html="page.title"></h1>
      <div class="page-content" v-html="page.content">
      </div>
  </div>
</template>

<script>
import FeedbackForm from "../Feedback/FeedbackForm";

export default {
  name: "Page",
  components: {FeedbackForm},
  props: {
    url: {
      required: true,
      type: String,
    }
  },
  beforeMount() {
    this.$store.dispatch("page/getByUrl", this.url);
  },
  watch: {
    url(newUrl){
      this.$store.dispatch("page/getByUrl", newUrl);
    },
  },
  computed:{
    page(){
      return this.$store.getters['page/page'];
    }
  },
}
</script>

<style scoped>

</style>