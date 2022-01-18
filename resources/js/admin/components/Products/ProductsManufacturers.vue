<template>
  <div class="wrapper-content">
    <h1>Производители товаров</h1>
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
    <div class="list" v-if="itemsData">
      <ProductsManufacturersItem class="list-item" v-for="(item, keyItem) in itemsData"
                                 :key="$root.guid()"
                                 :item="item"
                                 v-on:item-removed="itemsData.splice(keyItem, 1)"
                                 v-on:item-changed="changeItem(keyItem, $event)"
      >

      </ProductsManufacturersItem>
    </div>
  </div>
</template>

<script>
import ProductsManufacturersItem from "./ProductsManufacturersItem.vue";
import api from "../../common/api";

export default {
  name: "ProductsManufacturers",
  components: {
    ProductsManufacturersItem,
  },
  data() {
    return {
      loading: false,
      error: null,
      itemsData: [],
    }
  },
  created() {
    this.getItemsData();
  },
  watch: {},
  methods: {
    changeItem(keyItem, newVal) {
      if (this.itemsData[keyItem]) {
        this.itemsData[keyItem] = newVal;
      }
    },
    addItem() {
      if (!this.itemsData) {
        this.itemsData = [];
      }
      this.itemsData.unshift({'name': '', 'image': '', 'description': '',})
    },
    getItemsData() {
      this.loading = true;
      api.getData('productManufacturer', {})
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
    }
  }
}
</script>

<style scoped>

</style>