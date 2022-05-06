<template>
  <div class="form">
    <h1><span v-if="item.id">Редактирование</span><span v-else>Создание</span> категории оптового покупателя <span v-if="item.name"
                                                                                            v-html="item.name"></span>
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
        <label for="name">Название:</label>
        <input id="name" class="input" type="text" v-model="item.name">
      </div>
    </div>
    <div class="form-section">
      <div class="input-block input-block_highlight">
        <label for="amount">Скидка по умолчанию(%):</label>
        <input id="name" class="input" type="number" min="1" step="1" v-model="item.discount_default">
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";

export default {
  name: "wholesaleLevel",
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
        name: '',
        discount_default: 0,
      }
    }
    this.conditionsPrepare();
  },
  watch: {
    'item.discount_default'(newVal) {
      let discount = parseInt(newVal)
      if(isNaN(discount)){
        discount = 0;
      }
      this.item.discount_default = discount;
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
      api.getData('wholesaleLevel', {'where': 'id', 'searchString': this.itemId})
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
      api.applyData('wholesaleLevel', 'save', this.item)
          .then((r) => {
            if (r.result === true) {
              this.saveStatus = 1;
              this.$router.push({'name': 'WholesaleLevel'});
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
        api.applyData('wholesaleLevel', 'delete', {'id': this.item.id})
            .then((r) => {
              if (r.result === true) {
                this.$router.push({'name': 'WholesaleLevel'});
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