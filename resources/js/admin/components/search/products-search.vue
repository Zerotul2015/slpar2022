<template>
  <div class="products-search">
    <div class="products-search__block">
      <label for="products-search__input">Поиск по названию:</label>
      <div class="products-search__wrap">
        <input id="products-search__input" class="input products-search__input" v-model="valueForSearch"
               type="text" placeholder="введите название">
        <div class="products-search__founded" v-if="foundValues.length">
          <div v-for="(product,index) in foundValues" v-on:click="productAdd(product)"
               class="products-search__product" :data="product.id">
            <img class="products-search__product__image" v-if="product.image_main"
                 :src="'/images/products/' + product.folder + '/small/' + product.image_main">
            <img class="products-search__product__image" v-else="product.image_main"
                 src="/images/other/noimg.png">
            <div class="products-search__product__name">{{ product.name }}({{ product.price }}р.)</div>
          </div>
        </div>
      </div>
    </div>
    <div class="products-search__related_products">
      <div class="products-search__related-product-item" v-for="(relatedItem, idRelated) in itemProducts"
           @click="productRemove(idRelated)">
        <div class="products-search__related-product-item__name" v-html="relatedItem.name"></div>
        <img class="products-search__related-product-item__image" v-if="relatedItem.image_main"
             :src="'/images/products/' + relatedItem.folder + '/thumb/' + relatedItem.image_main">
        <img class="products-search__related-product-item__image" v-else="relatedItem.image_main"
             src="/images/other/noimg.png">
        <div class="products-search__related-product-item__delete-text">нажмите чтобы удалить</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import debounce from 'lodash/debounce'

export default {
  name: "products-search",
  props: {
    itemProducts: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      valueForSearch: '', //значения для поиска
      foundValues: [], // массив объектов из найденых значений
    }
  },
  created: function () {
    this.debouncedSearchProduct = debounce(this.searchProduct, 500);
  },
  watch: {
    itemProducts:function(){},
    valueForSearch: async function () {
      if (this.valueForSearch) {
        if (this.valueForSearch.length > 2) {
          this.debouncedSearchProduct();
        }
      } else {
        this.foundValues = [];
      }

    },
  },
  methods: {
    searchProduct: async function () {
      let that = this;
      await axios.post(`/admin/ajax/json/products-name-contains/`, {
        name: that.valueForSearch
      })
          .then(function (response) {
            that.foundValues = response.data;
          })
          .catch(function (error) {
            console.log('Ошибка запроса', error);
          });

    },
    productAdd: function (product) {
      console.log('добавили в компоненте');
      this.$emit('product-add', product);
    },
    productRemove: function (productID) {
      this.$emit('product-remove', productID)
    },
  }
}
</script>

<style scoped>

</style>