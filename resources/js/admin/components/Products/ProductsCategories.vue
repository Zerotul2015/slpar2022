<template>
  <div class="wrapper-content">
    <h1 v-html="titleCategories">Категории товаров</h1>
    <router-link v-if="parentId_" to="/products/categories" style="margin-top: -1rem;">вернуться к основным категориям
    </router-link>
    <div class="buttons-block">
      <button class="button button_green" @click="addItem">
        <span class="button-icon"><i class="far fa-plus"></i></span>
        <span class="button-text">Добавить</span>
      </button>
    </div>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="grid" v-if="itemsData">
      <ProductsCategory v-for="(item, keyItem) in itemsData"
                        :key="$root.guid()"
                        :item="item"
                        v-on:item-removed="itemsData.splice(keyItem, 1)"
                        v-on:item-changed="changeItem(keyItem, $event)"
      >
      </ProductsCategory>
    </div>
  </div>
</template>

<script>
import ProductsCategory from "./ProductsCategory.vue";
import api from "../../common/api";

export default {
  name: "ProductsCategories",
  props: {
    parentId: {}
  },
  components: {
    ProductsCategory,
  },
  data() {
    return {
      loading: false,
      error: null,
      itemsData: [],
      parentId_: 0,
      nameParent: '',
    }
  },
  created() {
    this.parentId_ = this.parentId ? this.parentId : 0;
    this.$store.dispatch('wholesaleLevel/getAll');
    this.$store.dispatch('productCategory/getAllById');
    this.getItemsData();
    this.getNameParent();
  },
  computed: {
    titleCategories() {
      let returnTitle = '';
      if (this.parentId_ === 0) {
        returnTitle = 'Категории товаров';
      } else {
        returnTitle = 'Подкатегории для ' + this.nameParent;
      }
      return returnTitle;
    },
  },
  watch: {
    itemsData: {
      deep: true,
      handler() {
      }
    },
    parentId_() {
      this.getNameParent();
      this.getItemsData();
      this.$store.dispatch('productCategory/getAllById');
    },
    parentId(newVal) {
      this.parentId_ = newVal ? newVal : 0;
    },
    nameParent(newVal) {
    },

  },
  methods: {
    changeItem(keyItem, newVal) {
      if (this.itemsData[keyItem]) {
        this.itemsData[keyItem] = newVal;
      }
    },
    addItem() {
      if (!this.itemsData) {
        this.itemsData = []
      }
      this.itemsData.unshift(
          {
            'name': '',
            'image': '',
            'description': '',
            'priority': 0,
            'parent_id': this.parentId_,
            'is_custom':false,
            'custom_link': null,
            'seo': {'title': '', 'description': ''},
            'binding_style':'',
            'wholesale_discount_size':{},
          }
      )
    },
    getItemsData() {
      this.loading = true;
      api.getData('productCategory', {'where': 'parent_id', 'searchString': this.parentId_})
          .then((r) => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.itemsData = r.returnData;
            } else {
              this.error = 'Ошибка загрузки данных:' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = 'Ошибка загрузки данных:' + e;
          })
    },
    getNameParent() {
      if (this.parentId_ !== 0) {
        this.loading = true;
        api.getData('productCategory', {'where': 'id', 'searchString': this.parentId_})
            .then((r) => {
              this.loading = false;
              if (r.result && r.result === true && r.returnData[0] && r.returnData[0].name) {
                this.nameParent = r.returnData[0].name;
              } else {
                this.error = 'Ошибка загрузки данных:' + r.error ? r.error : 'неизвестная ошибка';
              }
            })
            .catch((e) => {
              this.loading = false;
              this.error = 'Ошибка загрузки данных:' + e;
            })
      }
    }
  }
}
</script>

<style scoped>

</style>