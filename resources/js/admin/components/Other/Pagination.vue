<template>
  <div class="pagination" v-if="(pageCount_ > 1 && pageCount_ < 10)">
    <div class="pagination-title">Выберите страницу:</div>
    <router-link class="pagination-link" v-for="n in pageCount_"
                 :class="{'pagination-link-current':(pageNumber === n)}"
                 :to="urlPrefix + n"
                 :key="$root.guid()">
      {{ pageNumber }}
    </router-link>
  </div>
  <div class="pagination" v-else-if="pageCount_ !== 1">
    <div class="pagination-title">Выберите страницу:</div>
    <router-link class="pagination-link" v-for="n in 3"
                 :class="{'pagination-link-current':(pageNumber === n)}"
                 :to="urlPrefix + n"
                 :key="$root.guid()">
      {{ n }}
    </router-link>
    <div class="pagination-link-current" v-if="pageNumber>5">...</div>
    <router-link class="pagination-link" v-if="pageNumber>4"
                 :to="urlPrefix + (pageNumber - 1)"
                 :key="$root.guid()">
      {{ (pageNumber - 1) }}
    </router-link>
    <div class="pagination-link-current" v-if="pageNumber >3 && pageNumber<=(pageCount_-3)">{{ pageNumber }}</div>
    <router-link class="pagination-link" v-if="pageNumber<(pageCount_-3) && pageNumber > 2"
                 :to="urlPrefix + (pageNumber + 1)"
                 :key="$root.guid()">
      {{pageNumber + 1}}
    </router-link>
    <div class="pagination-link-current" v-if="pageNumber<(pageCount_-4)">...</div>
    <router-link class="pagination-link" v-for="n in 3"
                 :class="{'pagination-link-current':(pageNumber === pageCount_ - 3 + n)}"
                 :to="urlPrefix + (pageCount_ - 3 + n)"
                 :key="$root.guid()">
      {{ (pageCount_ - 3 + n) }}
    </router-link>
  </div>
</template>

<script>
export default {
  name: "Pagination",
  props: {
    pageCount: {},
    routePrefix: {},
    linkPrefix: {},
    //pageCurrentNumber: {},
  },
  data() {
    return {
      pageCount_: 0,
      routePrefix_: 1,
      linkPrefix_: 1,
      //pageCurrentNumber_: 1,
    }
  },
  created() {
    console.log(this.pageCount);
    this.pageCount_ = !this.pageCount ? 1: this.pageCount;
    //this.pageCurrentNumber_ = !!this.pageCurrentNumber ? this.pageCurrentNumber : 1;
  },
  watch:{
    pageCount(newVal){
      this.pageCount_ = !newVal ? 1: newVal;
    }
  },
  computed: {
    urlPrefix() {
      let prefix = '/';
      if (!!this.routePrefix) {
        prefix = this.routePrefix;
      } else {
        if (!!this.linkPrefix) {
          prefix = this.linkPrefix;
        }
      }
      return prefix
    },
    pageNumber() {
      let pageNumber = this.$route.params.pageNumber ? parseInt(this.$route.params.pageNumber) : 1;
      pageNumber = isNaN(pageNumber) || pageNumber === 0 ? 1 : pageNumber;
      pageNumber = pageNumber >= this.paginationPageCount ? this.paginationPageCount : pageNumber;
      return pageNumber;
    },
  }
}
</script>

<style scoped>

</style>