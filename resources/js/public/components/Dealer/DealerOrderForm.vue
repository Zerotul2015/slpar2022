<template>
  <div class="dealer-page">
    <breadcrumb></breadcrumb>
    <div class="dealer-home">
      <h1>Бланк заказа</h1>
      <div class="dh-order-blank" v-if="isAuth && products">
        <div v-for="(productItem) in products" :key="$root.guid()" class="ob-product-item">
          <img class="ob-product-img" :src="'/images/products/' + productItem.folder + '/thumb/' +productItem.image_main">
          <div class="ob-product-name">{{productItem.name}}</div>
          <div class="ob-product-count">
            <input class="input" type="number" step="1" placeholder="0">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "../Breadcrumb";
import IconSvg from "../Icon-svg/icon-svg";

export default {
  name: "DealerOrderForm",
  components: {IconSvg, Breadcrumb},
  data() {
    return {}
  },
  beforeMount() {
    this.$store.dispatch('productCategory/getProducts',);
  },
  watch:{
    products(){}
  },
  computed:{
    isAuth(){
      return this.$store.getters['customer/isAuth'];
    },
    products(){
      return this.$store.getters['productCategory/products']
    }
  }

}
</script>

<style scoped>

</style>