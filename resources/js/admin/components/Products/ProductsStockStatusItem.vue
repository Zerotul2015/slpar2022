<template>
  <div class="form-section list-item_not-hover">
    <div class="input-block error" v-if="!item.id" style="flex-shrink: 0;width:100%">*Это новый статус, он еще не
      сохранен!
    </div>
    <div class="input-block">
      <label :for="'name-' + guid">Наименование:</label>
      <input :id="'name-' + guid" class="input" type="text" v-model="item.name">
    </div>
    <div class="input-block">
      <label :for="'description-' + guid">Описание:</label>
      <input :id="'description-' + guid" class="input" type="text" v-model="item.description">
    </div>
    <div class="input-block">
      <label :for="'delivery_time-' + guid">Срок доставки:</label>
      <input :id="'delivery_time-' + guid" class="input" type="number" v-model="item.delivery_time">
    </div>
    <div class="buttons-block">
      <button class="button button_green" v-html="saveButtonText" @click="saveItem"></button>
      <button class="button button_remove" v-html="deleteButtonText" @click="removeItem"></button>
      <div class="error" v-html="errorText"></div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";

export default {
  name: "ProductsStockStatusItem",
  props: {
    item: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      guid: this.$root.guid(),
      errorText: '',
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
  created() {

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
    removeItem() {
      this.errorText = '';
      if (this.deleteStatus === 1) {
        api.applyData('productStockStatus', 'delete', {'id': this.item.id})
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
    },
    saveItem() {
      this.errorText = '';
      api.applyData('productStockStatus', 'save', this.item)
          .then((r) => {
            if (r.result === true) {
              this.saveStatus = 1;
              this.item.id = r.returnData.id ? r.returnData.id : undefined;
            } else {
              this.saveStatus = 2;
              this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            this.errorText = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    }
  }

}
</script>

<style scoped>

</style>