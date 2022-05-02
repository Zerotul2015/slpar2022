<template>
  <div class="form">
    <h1><span v-if="item.id">Редактирование</span><span v-else>Создание</span> промокода <span v-if="item.code_text"
                                                                                            v-html="item.code_text"></span>
    </h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="form-section">
      <div class="buttons-block">
        <button class="button button_green" v-html="saveButtonText" @click="saveItem"></button>
        <button class="button button_remove" v-html="deleteButtonText" @click="deleteItem">
        </button>
        <br>
        <div v-html="errorSaveText"></div>
        <div v-html="errorDeleteText"></div>
      </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_highlight">
        <label for="name">Промокод:</label>
        <input id="name" class="input" type="text" v-model="item.code_text">
      </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_highlight">
        <label for="date-start">Действует с:</label>
        <input id="date-start" type="date" v-model="item.date_start">
        <label for="date-end">до:</label>
        <input id="date-end" type="date" v-model="item.date_end">
      </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_highlight">
        <label for="amount">Размер скидки:</label>
        <input id="name" class="input" type="number" min="0.5" step="0.5" v-model="item.amount">
      </div>
      <div class="input-block input-block_highlight">
        <label for="unit">Ед. изм.:</label>
        <select id="unit" class="select" v-model="item.unit">
          <option value="rub">рубли</option>
          <option value="percent">проценты</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
export default {
  name: "PromoCode",
  props: {
    itemId: {
      required: false
    }
  },
  data() {
    return {
      loading: false,
      error: null,
      item: {},
      errorSaveText: null, //текст ошибки при  сохранения
      errorDeleteText: null, //текст ошибки удаления
      conditions: {},
      conditionsMinCount: 0,
      conditionsMinSum: 0,
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-icon">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-icon">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-trash-alt"></i></span><span class="button-icon">ошибка при удалии</span>',
      //end save, delete
    }
  },
  beforeMount() {
    if (this.itemId) {
      this.getItem();
    } else {
      this.item = {
        'date_start':null,
        'date_end':null,
        'code_text':null,
        'unit': 'percent',
        'amount': 0
      }
    }
  },
  watch: {
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
    getItem: async function () {
      api.getData('promoCode', {'where': 'id', 'searchString': this.itemId})
          .then((r) => {
            this.loading = false;
            if (r.result === true) {
              this.item = r.returnData[0];
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    },
    saveItem: async function () {
      api.applyData('promoCode', 'save', this.item)
          .then((r) => {
            if (r.result === true) {
              this.saveStatus = 1;
              this.$router.push({'name': 'PromoCodeList'});
            } else {
              this.saveStatus = 2;
              this.errorSaveText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            this.errorSaveText = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    },
    deleteItem() {
      if (this.deleteStatus === 1) {
        api.applyData('promoCode', 'delete', {'id': this.item.id})
            .then((r) => {
              if (r.result === true) {
                this.$router.push({'name': 'PromoCodeList'});
              } else {
                this.deleteStatus = 2;
                this.errorDeleteText = r.error ? r.error : 'неизвестная ошибка: ' + r;
              }
            })
            .catch((e) => {
              this.deleteStatus = 2;
              this.errorDeleteText = e.error ? e.error : 'неизвестная ошибка: ' + e;
            })
      } else {
        if (this.item.id) {
          this.deleteStatus = 1;
        } else {
          this.$router.go(-1);
        }
      }
    },
  }
}
</script>

<style scoped>

</style>