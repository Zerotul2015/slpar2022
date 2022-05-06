<template>
  <div class="grid list-item-customer">
    <div class="customer-id">{{ item.id }}</div>
    <div class="customer-name">{{ item.name }}</div>
    <div class="customer-mail"><a :href="'mailto:' + item.mail" title="написать письмо">{{ item.mail }}</a></div>
    <div class="customer-phone">{{item.phone}}</div>
    <div class="customer-act buttons-block">
      <button class="button button_green" v-if="changed" v-html="saveButtonText" @click="saveItem"></button>
      <router-link class="button" :to="'/customers/details/' + item.id" title="подробонее">
        <span class="button-icon"><i class="far fa-id-card"></i></span>
        <span class="button-text">подробнее</span>
      </router-link>
      <router-link class="button" :to="'/orders/customer/' + item.id" title="заказы">
        <span class="button-icon"><i class="far fa-shopping-basket"></i></span>
        <span class="button-text">заказы</span>
      </router-link>
<!--      <router-link class="button" :to="'/customers/сompanies/' + item.id" title="контрагенты">-->
<!--        <span class="button-icon"><i class="far fa-user-tie"></i></span>-->
<!--        <span class="button-text">Контрагенты</span>-->
<!--      </router-link>-->
      <button class="button button_red" v-html="deleteButtonText" @click="removeItem"></button>
      <div class="button-block-error" v-if="errorText" v-html="errorText"></div>
    </div>
  </div>
</template>
<script>
import api from "../../common/api";
export default {
  name: "CustomersListItem",
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      guid: this.$root.guid(),
      changed: false,
      errorText: "",
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-text">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-user-check"></i></span><span class="button-text">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-text">ошибка при сохранении</span>',
      deleteStatus: null, // 1 -  требуется подтверждение, 2 - ошибка, 0 | null - без изменений
      deleteButtonDefault: '<span class="button-icon"><i class="far fa-user-alt-slash"></i></span><span class="button-text">удалить</span>',
      deleteButtonConfirm: '<span class="button-icon"><i class="far fa-user-alt-slash"></i></span><span class="button-text">подтвердить удаление</span>',
      deleteButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при удалении</span>',
      //end save, delete
    }
  },
  watch:{
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
    saveItem() {
      this.errorText = '';
      api.applyData('customers', 'save', this.item)
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
        api.applyData('customers', 'delete', {'id': this.item.id})
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
.list-item-customer {
  display: grid;
  grid-template-areas: 'customer-id customer-name customer-mail customer-phone customer-act';
  grid-template-columns: repeat(5, auto) max-content;
  justify-content: start;
  padding: .5rem;
}

.customer-id {
  grid-area: customer-id;
}
.customer-name {
  grid-area: customer-name;
}

.customer-mail {
  grid-area: customer-mail;
}

.customer-phone {
  grid-area: customer-phone;
}

.customer-act {
  grid-area: customer-act;
  justify-content: end;
}
</style>