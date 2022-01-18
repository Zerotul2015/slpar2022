<template>
  <div class="products-search">
    <div class="products-search__block">
      <label for="products-search__input">Введите название:</label>
      <div class="products-search__wrap">
        <input id="products-search__input" class="input products-search__input" v-model="valueForSearch"
               type="text" placeholder="введите название">
        <div class="products-search__founded" v-if="foundValues.length">
          <div v-for="(category,index) in foundValues" v-on:click="categoryAdd(category)"
               class="products-search__product" :data="category.id">
            <div class="products-search__product__name">{{ category.name }}</div>
          </div>
        </div>
      </div>
    </div>
    <div class="products-search__related_products">
      <div class="products-search__related-product-item" v-for="(categoryItem, idCategory) in itemCategories"
           @click="categoryRemove(idCategory)">
        <div class="products-search__related-product-item__name" v-html="categoryItem.name"></div>
        <div class="products-search__related-product-item__delete-text">нажмите чтобы удалить</div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import debounce from 'lodash/debounce'

export default {
  name: "categories-search",
  props: {
    itemCategories: {

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
    this.debouncedSearchCategory = debounce(this.searchCategory, 500);
  },
  watch: {
    itemCategories:function(){},
    valueForSearch: async function () {
      if (this.valueForSearch) {
        if (this.valueForSearch.length > 2) {
          this.debouncedSearchCategory();
        }
      } else {
        this.foundValues = [];
      }

    },
  },
  methods: {
    searchCategory: async function () {
      let that = this;
      await axios.post(`/admin/ajax/json/categories-name-contains/`, {
        name: that.valueForSearch
      })
          .then(function (response) {
            that.foundValues = response.data;
          })
          .catch(function (error) {
            console.log('Ошибка запроса', error);
          });

    },
    categoryAdd: function (category) {
      console.log('добавили в компоненте');
      this.$emit('category-add', category);
    },
    categoryRemove: function (categoryID) {
      this.$emit('category-remove', categoryID)
    },
  }
}
</script>

<style scoped>

</style>