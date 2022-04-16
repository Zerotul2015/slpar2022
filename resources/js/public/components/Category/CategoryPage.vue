<template>
  <div class="catalog">
    <breadcrumb></breadcrumb>
    <div class="catalog-wrap">
      <h1 v-html="categoryName"></h1>
      <div class="catalog-products">
        <ProductCard v-for="(productItem) in products" :product="productItem" :key="$root.guid()"></ProductCard>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "../Breadcrumb";
import ProductCard from "../Product/ProductCard";

export default {
  name: "CategoryPage",
  components: {ProductCard, Breadcrumb},
  props: {
    url: {
      required: true,
      type: String,
    },
    urlParent: {
      required: false,
      type: String,
    }
  },
  beforeMount() {
    this.$store.dispatch("productCategory/getCategoryByUrl", this.url);
  },
  watch: {
    categoryName(){
      this.$store.dispatch("productCategory/getProducts");
    }
  },
  computed: {
    categoryName() {
      return this.$store.getters['productCategory/name'];
    },
    categoryDescription() {
      return this.$store.getters['productCategory/description'];
    },
    category() {

    },
    products() {
      return this.$store.getters['productCategory/products'];
    }
  },
  methods: {}
}
</script>

<style scoped>

</style>