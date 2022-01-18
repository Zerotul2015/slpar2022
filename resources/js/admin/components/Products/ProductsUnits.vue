<template>
  <div class="wrapper-content">
    <h1>Единицы измерения товаров</h1>
    <div class="buttons-block">
      <button class="button button_green" @click="addUnit">
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
    <div class="list" v-if="units">
      <ProductsUnitsItem class="list-item" v-for="(unitItem, keyItem) in units"
                         :key="$root.guid()"
                         :unitItem="unitItem"
                         v-on:item-removed="units.splice(keyItem, 1)"
                         v-on:item-changed="changeItem(keyItem, $event)"
      >

      </ProductsUnitsItem>
    </div>
  </div>
</template>

<script>
import ProductsUnitsItem from "./ProductsUnitsItem.vue";
import api from "../../common/api";

export default {
  name: "ProductsUnits",
  components: {
    ProductsUnitsItem,
  },
  data() {
    return {
      loading: false,
      error: null,
      units: [],
    }
  },
  created() {
    this.getUnits();
  },
  watch: {},
  methods: {
    changeItem(keyItem, newVal) {
      if (this.units[keyItem]) {
        this.units[keyItem] = newVal;
      }
    },
    addUnit() {
      this.units.unshift({'code': '', 'name': '', 'symbol_national': '', 'symbol_international': '',})
    },
    getUnits() {
      this.loading = true;
      api.getData('productUnit', {})
          .then((r) => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.units = r.returnData;
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