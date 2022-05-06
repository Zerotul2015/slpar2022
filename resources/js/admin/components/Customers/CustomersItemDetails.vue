<template>
  <div class="wrapper-content">
    <h1 v-html="titleText"></h1>
    <div class="wrapper-content">
      <div class="buttons-block">
        <button class="button button_green" v-html="saveButtonText" @click="saveItem"></button>
        <button class="button button_red" v-html="deleteButtonText" @click="removeItem"></button>
        <div class="button-block-error" v-if="errorText" v-html="errorText"></div>
      </div>
      <h2>Основные данные</h2>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-status-' +guid">
            Статус:
          </label>
          <select class="select" :id="'customer-status-' +guid" v-model="customer.status">
            <option v-for="(statusName, statusKey) in customerStatuses" :key="statusKey" :value="statusKey">{{
                statusName
              }}
            </option>
          </select>
        </div>
      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-is-wholesale-' +guid">
            Тип:
          </label>
          <select class="select" :id="'customer-is-wholesale-' +guid" v-model="customer.is_wholesale">
            <option :value="1">Оптовик</option>
            <option :value="0">Обычный покупатель</option>
          </select>
        </div>
      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-name-' +guid">
            Имя:
          </label>
          <input :id="'customer-name-' +guid" class="text" type="text" v-model.lazy="customer.name">
        </div>
      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-mail-' +guid">
            Почта:
          </label>
          <input :id="'customer-mail-' +guid" class="text" type="email" v-model.lazy="customer.mail">
        </div>
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-phone-' +guid">
            Телефон:
          </label>
          <input :id="'customer-phone-' +guid" class="text" type="tel" v-model.lazy="customer.phone">
        </div>
      </div>

      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-mail-' +guid">
            Пароль:
          </label>
          <button class="button button_small" v-if="changePass === false" @click="changePass = true">изменить</button>
          <input :id="'customer-pass-' +guid" class="text" type="text" v-else v-model.lazy="customer.pass">
        </div>

      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight input-block_column">
          <label class="label" :for="'customer-note-hidden-' +guid">
            Примечание(покупатель его не увидит):
          </label>
          <textarea :id="'customer-note-hidden-' +guid" class="text" type="email" v-model.lazy="customer.note_hidden">

        </textarea>
        </div>
      </div>
      <div class="grid" v-if="orders">
        <customers-orders-list :customer-id="customer.id"></customers-orders-list>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
import CustomersItemCompany from "./CustomersItemCompany";
import CustomersItemOrders from "./CustomersOrdersList";
import CustomersOrdersList from "./CustomersOrdersList";

export default {
  name: "CustomersItemDetails",
  components: {CustomersOrdersList, CustomersItemOrders, CustomersItemCompany},
  props: {
    customerId: {}
  },
  data() {
    return {
      guid: this.$root.guid(),
      changed: false,
      changePass: false,
      customer: {
        'name': '',
        'phone': '',
        'mail': '',
        'status': 'active',
        'note_hidden': '',
        'is_wholesale': false,
      },
      customerStatuses: {
        'active': 'Активен',
        'blocked': 'Заблокирован',
        'confirm_wait': 'Ожидает подтверждения',
      },
      // companyNew: {
      //   'customer_id': this.customerId,
      //   'name': '',
      //   'inn': '',
      //   'kpp': '',
      //   'note': '',
      //   'note_hidden': '',
      // },
      errorText: "",
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-text">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-user-check"></i></span><span class="button-text">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-text">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-user-alt-slash"></i></span><span class="button-text">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-user-alt-slash"></i></span><span class="button-text">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-text">ошибка при удалении</span>',
      //end save, delete
    }
  },
  beforeMount() {
    if (this.customerId) {
      this.getItem();
    }
  },
  watch: {
    changePass(newVal) {
      if (newVal === true) {
        this.customer.pass = Math.random().toString(36).slice(-8);
      }
    }
  },
  computed: {
    titleText() {
      let text = 'Создание ';
      if (this.customer.id) {
        text = 'Редактирование ';
      }
      if (this.customer.is_wholesale) {
        text = text + 'оптового';
      }
      text = text + ' покпателя' + this.customer.name;
    },
    orders() {
      return this.$store.getters['customer/orders'];
    },
    company() {
      return this.$store.getters['customer/company'];
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
  }
  ,
  methods: {
    getItem: async function () {
      api.getData('customer', {'where': 'id', 'searchString': this.customerId})
          .then((r) => {
            this.loading = false;
            if (r.result === true) {
              this.customer = r.returnData[0];
              this.$store.dispatch('customer/setCustomerData', this.customer);
              this.$store.dispatch('customer/getRelatedData');
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    },
    saveItem() {
      this.errorText = '';
      api.applyData('customer', 'save', this.customer)
          .then((r) => {
            if (r.result && r.result === true) {
              setTimeout(() => {
                this.changed = false;
              }, 1500);
              this.saveStatus = 1;
              this.customer.id = r.returnData.id ? r.returnData.id : undefined;
              this.$store.dispatch('customer/getRelatedData');
            } else {
              this.saveStatus = 2;
              this.errorText = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          }).catch((e) => {
        this.saveStatus = 2;
        this.errorText = e.error ? e.error : 'неизвестная ошибка: ' + e;
      })
    }
    ,
    removeItem() {
      this.errorText = '';
      if (this.deleteStatus === 1) {
        api.applyData('customer', 'delete', {'id': this.customer.id})
            .then((r) => {
              if (r.result && r.result === true) {
                this.deleteStatus = 1;
                this.$router.go(-1);
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
        if (this.customer.id) {
          this.deleteStatus = 1;
        } else {
          this.$router.go(-1);
        }
      }
    }
  }
}
</script>

<style scoped>
.component-fade-enter-active, .component-fade-leave-active {
  transition: opacity .3s ease;
}

.component-fade-enter, .component-fade-leave-to
  /* .component-fade-leave-active до версии 2.1.8 */
{
  opacity: 0;
}
</style>