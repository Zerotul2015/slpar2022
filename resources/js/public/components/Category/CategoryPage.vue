<template>
    <div class="main-content catalog-wrap">
      <h1 v-html="categoryName + textCategoryWithFilter"></h1>
      <div class="catalog-products">
        <ProductCard v-for="(productItem) in filteredProducts" :product="productItem" :key="$root.guid()"
                     :image-size="sizeImageProduct"></ProductCard>
      </div>
    </div>
</template>

<script>
import ProductCard from "../Product/ProductCard";

export default {
  name: "CategoryPage",
  components: {ProductCard},
  props: {
    url: {
      required: true,
      type: String,
    },
    urlParent: {
      required: false,
      type: String,
    },
    styleUrl: {
      required: false,
      type: String,
    }
  },
  data() {
    return {}
  },
  beforeMount() {
    this.$store.dispatch("productCategory/getCategoryByUrl", this.url);
    if (this.styleUrl) {
      this.$store.dispatch("bathStyle/setActiveStyleByUrl", this.styleUrl);
    }
  },
  watch: {
    bathStyleActiveId() {
    },
    url(newUrl) {
      this.$store.dispatch("productCategory/getCategoryByUrl", newUrl);
      this.$store.dispatch("productCategory/getProducts");
    },
    styleUrl(newUrl) {
      this.$store.dispatch("bathStyle/setActiveStyleByUrl", newUrl);
    },
    categoryName() {
    }
  },
  computed: {
    gridSize() {
      return this.$store.getters['templateData/gridSizeCategory'];
    },
    sizeImageProduct() {
      let size = 'thumb';
      if (this.gridSize === 'big') {
        size = 'thumb_medium';
      }
      return size;
    },
    categoryName() {
     return this.$store.getters['productCategory/name'];
    },
    categoryDescription() {
      return this.$store.getters['productCategory/description'];
    },
    category() {
      return this.$store.getters['productCategory/category'];
    },
    products() {
      return this.$store.getters['productCategory/products'];
    },
    bathStyles() {
      return this.$store.getters['bathStyle/all'];
    },
    bathStyleActiveKey() {
      return this.$store.getters['bathStyle/activeKey'];
    },
    bathStyleActiveId() {
      return this.$store.getters['bathStyle/activeId'];
    },
    toggleFilterByStyle() {
      return this.$store.getters['bathStyle/toggleFilterForCategory']
    },
    textCategoryWithFilter() {
      let text = '';
      if (this.toggleFilterByStyle) {
        text = text + ' в стиле "' + this.bathStyles[this.bathStyleActiveKey].name + '"';
      }
      return text;
    },
    filteredProducts() {
      let products = [];
      if (this.toggleFilterByStyle) {
        products = this.products.filter((item, index, array) => {
          let stylesInProduct = item.bath_style_id;
          return this.bathStyleActiveId && !!stylesInProduct && stylesInProduct.includes(this.bathStyleActiveId);
        });
      } else {
        products = this.products
      }
      return products;
    },
  },
  methods: {}
}
</script>

<style scoped>

</style>