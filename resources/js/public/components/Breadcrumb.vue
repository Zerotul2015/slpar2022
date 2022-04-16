<template>
  <div class="breadcrumbs-wrap" v-html="breadcrumb">

  </div>
</template>

<script>
export default {
  name: "Breadcrumb",
  computed: {
    breadcrumb() {
      return this.$store.getters["templateData/breadcrumb"];
    }
  }
}
</script>

<style scoped>

</style>