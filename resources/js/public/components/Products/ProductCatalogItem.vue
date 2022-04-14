<template>
<div class="c-product-item">
  <div class="c-p-favorite">
    <icon-svg icon="svg-favorite"></icon-svg>
  </div>
  <div class="c-p-title">{{productItem.name}}</div>
  <img class="c-p-image" :src="imageMain" :alt="productItem.name">
  <div class="c-p-price">
    <span class="price-to-locale" itemprop="price" v-html="productItem.price|priceToLocale"></span>
    <span class="price-currency" itemprop="priceCurrency" content="RUB">руб.</span>
  </div>
  <div class="c-p-buttons">
    <button class="btn">сравнить</button>
    <button class="btn btn_green">купить</button>
  </div>
</div>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";
export default {
  name: "ProductCatalogItem",
  components: {IconSvg},
  props:{
    productItem:{
      required:true,
      type:Object
    }
  },
  computed:{
    imageMain(){
      let imageUrl = '/build/images/noimg.png';
      if(this.productItem.image_main){
        imageUrl = '/images/products/' + this.productItem.folder + '/thumb/' +  this.productItem.image_main;
      }
      return imageUrl;
    }
  }
}
</script>

<style scoped>

</style>