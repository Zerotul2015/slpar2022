<template>
    <div class="catalog-wrap">
      <h1>Сравнение товаров</h1>
      <h2 v-if="countCompare < 1">Вы не добавили ни одного товара в сравнение. <router-link to="/">Перейти к готовым стилевым решениям.</router-link></h2>
      <div class="compare-group" v-for="(groupCat) in productsGroupByCat">
        <h2>{{groupCat.category.name}}</h2>
        <div class="catalog-products">
          <ProductCard v-for="(productItem) in groupCat.products" :product="productItem" :key="$root.guid()"
                       :image-size="sizeImageProduct"></ProductCard>
        </div>
      </div>
    </div>
</template>

<script>
import ProductCard from "../Product/ProductCard";

export default {
  name: "Compare",
  components: {ProductCard},
  beforeMount() {
    this.$store.dispatch("compare/getCompareWithCategories");
  },
  computed:{
    gridSize(){
      return this.$store.getters['templateData/gridSizeCompare'];
    },
    sizeImageProduct(){
      let size = 'thumb';
      if(this.gridSize ==='big'){
        size = 'thumb_medium';
      }
      return size;
    },
    countCompare(){
      return this.$store.getters['compare/count'];
    },
    productsGroupByCat(){
      return this.$store.getters['compare/productsGroupByCategory'];
    }
  }
}
</script>

<style scoped>

</style>