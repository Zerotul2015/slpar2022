<template>
  <transition name="component-fade" mode="out-in">
    <div class="products-grid__product">
      <img class="product-grid__image" :src="imageProduct" onerror="this.src='/build/images/noimg.png'">
      <div class="products-grid__product-name"  title="Название товара">{{ item.name }}</div>
      <div class="products-grid__product-article" title="Артикул товара">{{ item.article }}</div>
      <div class="products-grid__product-category">
        <select class="select" v-model="item.category_id">
          <option :value="null">Без категории</option>
          <option v-for="category in categories" :value="category.id">
            <span v-if="category.parent_id && !!categories[category.parent_id]">{{categories[category.parent_id].name}} -> </span>{{ category.name }}
          </option>
        </select>
      </div>
      <div class="products-grid__product-price input-block" v-if="item.price_on_request">
        <div class="label">Цена по запросу</div>
        <VueToggles @click="item.price_on_request = !item.price_on_request"
                    :value="!!item.price_on_request"
                    checkedText="да"
                    uncheckedText="нет"
                    checkedBg="#11994B"
        />
      </div>
      <div class="form-section products-grid__product-price" v-else>
        <div class="input-block">
          <label>Цена:</label>
          <input v-if="priceEdit" class="input" v-model="item.price" type="number" placeholder="цена в руб.">
          <div v-else-if="item.price" @click="priceEdit = true">{{ item.price |priceToLocale }} ₽</div>
          <div v-else @click="priceEdit = true">-</div>
        </div>
        <div class="input-block">
          <label>Цена(до скидки):</label>
          <input v-if="priceOldEdit" class="input" v-model="item.price_old" type="number" placeholder="цена в руб.">
          <div v-else-if="item.price_old" @click="priceOldEdit = true">{{ item.price_old |priceToLocale }} ₽</div>
          <div v-else @click="priceOldEdit = true">-</div>
        </div>
      </div>
      <div class="products-grid__product-dimension-link">
        <div class="link" @click="dimensionsShow = !dimensionsShow" v-html="dimensionsLink">габариты</div>
      </div>
      <div class="products-grid__product-action buttons-block justify-self-end">
        <button class="button button_green" v-if="changed" v-html="saveButtonText" @click="saveItem"></button>
        <router-link class="button" :to="'/products/edit/' + item.id" title="редактирование">
          <span class="button-icon"><i class="far fa-edit"></i></span>
        </router-link>
        <router-link class="button" :to="'/products/copy/' + item.id" title="копировать">
          <span class="button-icon"><i class="far fa-copy"></i></span>
        </router-link>
        <button class="button button_red" v-html="deleteButtonText" @click="removeItem"></button>
        <div class="button-block-error" v-html="errorText"></div>
      </div>
      <transition name="component-fade" mode="out-in">
        <div class="products-grid__product-dimensions" v-if="dimensionsShow">
          <div class="input-block input-block_column">
            <label :for="'dimension-length'+guid" class="label">Длина, м.:</label>
            <input :id="'dimension-length' +guid" class="input" type="text" v-model="item.dimensions.d_length">
          </div>
          <div class="input-block input-block_column">
            <label :for="'dimension-width'+guid" class="label">Ширина, м.:</label>
            <input :id="'dimension-width' +guid" class="input" type="text" v-model="item.dimensions.d_width">
          </div>
          <div class="input-block input-block_column">
            <label :for="'dimension-height'+guid" class="label">Высота, м.:</label>
            <input :id="'dimension-height' +guid" class="input" type="text" v-model="item.dimensions.d_height">
          </div>
          <div class="input-block input-block_column">
            <label :for="'dimension-weight'+guid" class="label">Вес, кг.:</label>
            <input :id="'dimension-weight' +guid" class="input" type="text" v-model="item.dimensions.d_weight">
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script>
import api from "../../common/api";
import VueToggles from "vue-toggles";

export default {
  name: "ProductsListItem",
  components: {
    VueToggles
  },
  props: {
    item: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      guid: this.$root.guid(),
      changed: false,
      priceEdit: false,
      priceOldEdit: false,
      dimensionsShow: false,
      errorText: "",
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">подтвердить</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-text">ошибка</span>',
      //end save, delete
    }
  },
  created() {
    if (!this.item.dimensions) {
      this.item.dimensions = {
        d_length: 0,
        d_width: 0,
        d_height: 0,
        d_weight: 0,
      }
    }
  },
  watch: {
    'item.stock_status'(newVal, oldVal) {
      this.changed = true;
    },
    'item.category_id'(newVal, oldVal) {
      this.changed = true;
    },
    'item.price'(newVal, oldVal) {
      this.changed = true;
      let priceVal = parseFloat(newVal);
      if (isNaN(priceVal)) {
        priceVal = 0;
      }
      this.item.price = priceVal;
    },
    'item.price_old'(newVal, oldVal) {
      this.changed = true;
      let priceVal = parseFloat(newVal);
      if (isNaN(priceVal)) {
        priceVal = 0;
      }
      this.item.price_old = priceVal;
    },
    'item.price_on_request'(newVal, oldVal) {
      this.changed = true;
    },
    //start save, delete
    saveStatus: function (newVal) {
      if (newVal) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    deleteStatus: function (newVal) {
      if (newVal === 2) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    //end save, delete
  },
  computed: {
    imageProduct() {
      let image = '';
      if (!this.item.image_main && !this.item.images[0]) {
        image = '/build/images/noimg.png';
      } else {
        if (this.item.image_main) {
          image = '/images/products/' + this.item.folder + '/thumb/' + this.item.image_main;
        } else {
          image = '/images/products/' + this.item.folder + '/thumb/' + this.item.images[0]
        }
      }
      return image;
    },
    dimensionsLink() {
      let returnText = 'показать габариты';
      if (this.dimensionsShow === true) {
        returnText = 'скрыть габариты';
      }
      return returnText;
    },
    categories() {
      return this.$store.state.productCategory.allById;
    },
    stockStatuses() {
      return this.$store.state.productStockStatus.allById;
    },
    //start save, delete
    saveButtonText() {
      if (this.saveStatus === 1) {
        return this.saveButtonSuccess;
      }
      if (this.saveStatus === 2) {
        return this.saveButtonError;
      }
      if (this.saveStatus === 0 || this.saveStatus === null) {
        return this.saveButtonDefault;
      }
    },
    deleteButtonText() {
      if (this.deleteStatus === 1) {
        return this.deleteButtonConfirm;
      }
      if (this.deleteStatus === 2) {
        return this.deleteButtonError;
      }
      if (this.deleteStatus === 0 || this.deleteStatus === null) {
        return this.deleteButtonDefault;
      }
    },
    //end save, delete
  },
  methods: {
    saveItem() {
      this.errorText = '';
      api.applyData('product', 'save', this.item)
          .then((r) => {
            if (r.result && r.result === true) {
              setTimeout(() => {
                this.changed = false;
              }, 1500);
              this.saveStatus = 1;
            } else {
              this.saveStatus = 2;
              this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          }).catch((e) => {
        this.saveStatus = 2;
        this.errorText = e.error ? e.error : 'неизвестная ошибка: ' + e;
      })
    },
    removeItem() {
      this.errorText = '';
      if (this.deleteStatus === 1) {
        api.applyData('product', 'delete', {'id': this.item.id})
            .then((r) => {
              if (r.result && r.result === true) {
                this.deleteStatus = 1;
                this.$emit('item-removed')
              } else {
                this.deleteStatus = 2;
                this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
              }
            })
            .catch((e) => {
              this.deleteStatus = 2;
              this.errorText = e ? e : 'неизвестная ошибка';
            });
      } else {
        if (this.item.id) {
          this.deleteStatus = 1;
        } else {
          this.$emit('item-removed')
        }
      }
    }
  }
}
</script>

<style scoped>
.products-grid__product {
  display: grid;
  grid-gap: 1rem;
  grid-template-areas: 'pr-image pr-name pr-article pr-cat pr-price pr-dl pr-action';
  grid-template-columns: 3rem 20rem 10rem auto 14rem 1fr max-content;
  grid-auto-flow: row;
  justify-content: start;
  padding: .5rem;
}

.product-grid__image {
  grid-area: pr-image;
  width: 3rem;
  display: block;
}

.products-grid__product-name {
  grid-area: pr-name;
  justify-items: start;
}

.products-grid__product-category {
  grid-area: pr-cat;
  display: grid;
  width: 100%;
}

.products-grid__product-article {
  grid-area: pr-article;
  display: grid;
}

.products-grid__product-price {
  grid-area: pr-price;
  display: grid;
}

.products-grid__product-dimension-link {
  grid-area: pr-dl;
  justify-self: end;
}

.products-grid__product-action {
  grid-area: pr-action;
  justify-items: end;
}

.products-grid__product-dimensions {
  /*grid-area: pr-d;*/
  display: grid;
  grid-auto-flow: column;
  justify-content: start;
  grid-gap: 1rem;
}

.component-fade-enter-active, .component-fade-leave-active {
  transition: opacity .3s ease;
}

.component-fade-enter, .component-fade-leave-to
  /* .component-fade-leave-active до версии 2.1.8 */
{
  opacity: 0;
}
</style>