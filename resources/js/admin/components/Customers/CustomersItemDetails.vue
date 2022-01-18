<template>
  <div class="wrapper-content">
    <h1 v-html="title"></h1>
    <div class="wrapper-content" v-if="!customerNotFound">
      <div class="buttons-block">
        <button class="button button_green" v-if="changed" v-html="saveButtonText" @click="saveItem"></button>
        <button class="button button_red" v-html="deleteButtonText" @click="removeItem"></button>
        <div class="button-block-error" v-if="errorText" v-html="errorText"></div>
      </div>
      <h2>Основные данные</h2>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-phone-' +guid">
            Статус:
          </label>
          <select class="select" v-model="customerStatus">
            <option v-for="(statusName, statusKey) in customerStatuses" :key="statusKey" :value="statusKey">{{
                statusName
              }}
            </option>
          </select>
        </div>
      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-name-' +guid">
            Имя:
          </label>
          <input :id="'customer-name-' +guid" class="text" type="text" v-model.lazy="customerName">
        </div>
      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-mail-' +guid">
            Почта:
          </label>
          <input :id="'customer-mail-' +guid" class="text" type="email" v-model.lazy="customerMail">
        </div>
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-phone-' +guid">
            Телефон:
          </label>
          <input :id="'customer-phone-' +guid" class="text" type="tel" v-model.lazy="customerPhone">
        </div>
      </div>

      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight">
          <label class="label" :for="'customer-mail-' +guid">
            Пароль:
          </label>
          <button class="button button_small" v-if="changePass === false" @click="changePass = true">изменить</button>
          <input :id="'customer-pass-' +guid" class="text" type="text" v-else v-model.lazy="customerPass">
        </div>

      </div>
      <div class="form-section form-section_column">
        <div class="input-block input-block_highlight input-block_column">
          <label class="label" :for="'customer-note-hidden-' +guid">
            Примечание(покупатель его не увидит):
          </label>
          <textarea :id="'customer-note-hidden-' +guid" class="text" type="email" v-model.lazy="customerNoteHidden">

        </textarea>
        </div>
      </div>
      <div class="grid" v-if="customerId && !customerNotFound">
        <h2>Последние 10 заказов</h2>
        <div class="buttons-block">
          <router-link class="button button_small button_green" to="/orders/create">
            <span class="button-icon"><i class="far fa-plus"></i></span>
            <span class="button-text">Создать заказ</span>
          </router-link>
          <router-link v-if="!orders || orders.length < 1" class="link" :to="'/orders/customer/' + customerId">
            <span class="link-text">все заказы</span>
          </router-link>
        </div>
        <div v-if="!orders || orders.length < 1">
          Покупатель еще ничего не заказывал.
        </div>
        <div class="grid background-alternation" v-else>
          <customers-item-orders :customer-id="customer.id"></customers-item-orders>
        </div>
      </div>
      <div class="grid" v-if="customerId && !customerNotFound">
        <h2>Контрагенты</h2>
        <div class="grid customer-company p-1">
          <button class="button button_small button_green" v-if="companyAddToggle === false" @click="companyAddToggle = true">
            <span class="button-text">добавить контрагента</span>
          </button>
          <transition name="component-fade" mode="out-in">
            <div class="form-section form-section_with-title background-green" v-if="companyAddToggle === true">
              <div class="form-title">
                Новый контрагент
              </div>
              <div class="form-section form-section_column">
                <div class="input-block input-block_column input-block_highlight">
                  <label class="label" :for="'new-company-name-' + guid">Наименование</label>
                  <input class="text" :id="'new-company-name-' + guid" type="text" v-model="companyNew.name">
                </div>
                <div class="input-block input-block_column input-block_highlight">
                  <label class="label" :for="'new-company-inn-' + guid">ИНН</label>
                  <input class="text" :id="'new-company-inn-' + guid" type="number" minlength="10" maxlength="12"
                         v-model="companyNew.inn">
                </div>
                <div class="input-block input-block_column input-block_highlight">
                  <label class="label" :for="'new-company-inn-' + guid">КПП</label>
                  <input class="text" :id="'new-company-kpp-' + guid" type="number" minlength="9" maxlength="9"
                         v-model="companyNew.kpp">
                </div>
              </div>
              <div class="buttons-block">
                <button class="button button_small button_green" @click="addCompany" v-html="addCompanyText"></button>
                <button class="button button_small" @click="companyAddToggle = false">
                  <span class="button-icon"><i class="far fa-times"></i></span>
                  <span class="button-text">отменить</span>
                </button>
                <div v-if="errorTextCompany.length > 0" class="errorText" v-html="errorTextCompany"></div>
              </div>
            </div>
          </transition>
        </div>
        <div v-if="!company || company.length < 1">
          У покупателя нет контрагенов.
        </div>
        <div class="grid background-alternation p-1" v-else>
          <customers-item-company v-for="(companyItem, companyKey) in company"
                                  :customer-id="customer.id"
                                  :company="companyItem"
                                  v-on:item-removed="$store.dispatch('customer/getCompany', {'id': customerId})"
                                  :key="$root.guid()">
          </customers-item-company>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from "../../common/api";
import CustomersItemCompany from "./CustomersItemCompany";
import CustomersItemOrders from "./CustomersItemOrders";

export default {
  name: "CustomersItemDetails",
  components: {CustomersItemOrders, CustomersItemCompany},
  props: {
    customerCreate: {
      type: Boolean
    },
    customerId: {}
  },
  data() {
    return {
      guid: this.$root.guid(),
      changed: false,
      changePass: false,
      customerNew: {
        'name': '',
        'phone': '',
        'mail': '',
        'pass': Math.random().toString(36).slice(-8),
        'status': 'active',
        'note_hidden': '',
      },
      customerStatuses: {
        'active': 'Активен',
        'blocked': 'Заблокирован',
        'confirm_wait': 'Ожидает подтверждения',
      },
      companyNew: {
        'customer_id': this.customerId,
        'name': '',
        'inn': '',
        'kpp': '',
        'note': '',
        'note_hidden': '',
      },
      companyAddToggle: false,
      errorTextCompany: "",
      errorText: "",
      addStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      addCompanyDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-text">сохранить контрагента</span>',
      addCompanySuccess: '<span class="button-icon"><i class="far fa-user-check"></i></span><span class="button-text">контрагент добавлен</span>',
      addCompanyError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-text">ошибка при добавить</span>\',',
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
  created() {
    if (this.customerId) {
      this.$store.dispatch('customer/getData', {'id': this.customerId});
    } else {
      this.changed = true;
      this.$store.dispatch('customer/reset');
    }
  },
  watch: {
    changePass(newVal) {
      if (newVal === true) {
        this.customerPass = Math.random().toString(36).slice(-8);
      }
    }
  },
  computed: {
    customerNotFound() {
      return this.$store.state.customer.notFound;
    },
    customerPass: {
      get() {
        return this.$store.state.customer.profile && this.$store.state.customer.profile.pass ? this.$store.state.customer.profile.pass : null;
      },
      set(newVal) {
        this.changed = true;
        let newValSet = this.customer;
        newValSet['pass'] = newVal;
        this.$store.commit('customer/setProfile', {'profile': newValSet, 'changedPass': true})
      }
    },
    customerName: {
      get() {
        return this.$store.state.customer.profile && this.$store.state.customer.profile.name ? this.$store.state.customer.profile.name : null;
      },
      set(newVal) {
        this.changed = true;
        let newValSet = this.customer;
        newValSet['name'] = newVal;
        this.$store.commit('customer/setProfile', {'profile': newValSet})
      }
    },
    customerPhone: {
      get() {
        return this.$store.state.customer.profile && this.$store.state.customer.profile.phone ? this.$store.state.customer.profile.phone : null;
      },
      set(newVal) {
        this.changed = true;
        let newValSet = this.customer;
        newValSet['phone'] = newVal;
        this.$store.commit('customer/setProfile', {'profile': newValSet})
      }
    },
    customerMail: {
      get() {
        return this.$store.state.customer.profile && this.$store.state.customer.profile.mail ? this.$store.state.customer.profile.mail : null;
      },
      set(newVal) {
        this.changed = true;
        let newValSet = this.customer;
        newValSet['mail'] = newVal;
        this.$store.commit('customer/setProfile', {'profile': newValSet})
      }
    },
    customerNoteHidden: {
      get() {
        return this.$store.state.customer.profile && this.$store.state.customer.profile.note_hidden ? this.$store.state.customer.profile.note_hidden : null;
      },
      set(newVal) {
        this.changed = true;
        let newValSet = this.customer;
        newValSet['note_hidden'] = newVal;
        this.$store.commit('customer/setProfile', {'profile': newValSet})
      }
    },
    customerStatus: {
      get() {
        return this.$store.state.customer.profile && this.$store.state.customer.profile.status ? this.$store.state.customer.profile.status : 'active';
      },
      set(newVal) {
        this.changed = true;
        let newValSet = this.customer;
        newValSet['status'] = newVal;
        this.$store.commit('customer/setProfile', {'profile': newValSet})
      }
    },
    customer: {
      get() {
        return this.$store.state.customer.profile;
      },
      set(newVal) {
        this.$store.commit('customer/setProfile', {'profile': newVal})
      }
    },
    orders() {
      return this.$store.state.customer.orders;
    },
    company() {
      return this.$store.state.customer.company;
    },
    title() {
      let titleText = '';
      if (this.customerCreate) {
        titleText = 'Создание нового покупателя';
      }
      if (this.customerId) {
        if (this.customerNotFound === true) {
          titleText = 'Покупателя с ID ' + this.customerId + ' не существует.';
        } else {
          titleText = 'Редактирование покупателя';
        }
      }
      return titleText;
    },
    addCompanyText() {
      if (this.addStatus === 1) {
        return this.addCompanySuccess;
      }
      if (this.addStatus === 2) {
        return this.addCompanyError;
      }
      if (this.addStatus === 0 || this.addStatus === null) {
        return this.addCompanyDefault;
      }
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
    async addCompany() {
      this.errorTextCompany = '';
      await api.applyData('customerCompany', 'save', this.companyNew)
          .then((r) => {
            console.log(r);
            if (r.result && r.result === true) {
              this.companyNew = {
                'customer_id': this.customerId,
                'name': '',
                'inn': '',
                'kpp': '',
                'note': '',
                'note_hidden': '',
              };
              this.$store.dispatch('customer/getCompany', {'id': this.customerId});
            } else {
              this.errorTextCompany = r.error ? r.error : 'неизвестная ошибка: ' + r;
            }
          }).catch((e) => {
            this.errorTextCompany = e.error ? e.error : 'неизвестная ошибка: ' + e;
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
              this.$store.commit('customer/setId', r.returnData.id ? r.returnData.id : undefined)
              this.$store.dispatch('customer/getData');
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