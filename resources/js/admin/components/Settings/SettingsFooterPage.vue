<template>
  <div class="template-footer-item input-block">
    <select class="select" v-model="pageId">
      <option v-for="(page, id) in pages"
              v-if="!$parent.fetchedData.template_footer[colKey].values[page.id] || pageId === page.id"
              :value="id">{{page.title}}</option>
    </select>
    <div class="buttons-block">
      <button class="button button_small button_remove" @click="removeItem">X</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "SettingsFooterPage",
  props: {
    pageId: {
      required: true
    },
    colKey:{
      required: true,
    }
  },
  computed: {
    pages() {
      return this.$store.state.page.allById;
    },
  },
  methods:{
    removeItem(){
      this.$emit('remove-page',this.colKey, this.pageId)
    }
  }
}
</script>

<style scoped>

</style>