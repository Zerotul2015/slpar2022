<template>
  <div class="making-order-block">
    <h2 class="order-block-title">Оформление заказа</h2>
    <div class="mob-form">
      <div class="mob-customer">
        <div class="mob-title">Данные покупателя</div>
        <div class="mob-input-block">
          <label for="name-input" class="label">Ваше полное имя:</label>
          <input id="name-input" type="text" placeholder="ФИО" v-model.lazy="name"
                 class="input" :class="{'input_error':!nameFilled && nameChange}">
        </div>
        <div class="mob-input-block">
          <label for="mail-input" class="label">E-mail:</label>
          <input id="mail-input" type="email" placeholder="yourmail@site.ru" v-model.lazy="mail"
                 class="input" :class="{'input_error':!mailIsCorrect && mailChange}">
        </div>
        <div class="mob-input-block">
          <label for="phone-input" class="label">Номер телефона:</label>
          <input id="phone-input" type="text" placeholder="9234551122" v-model.lazy="phone"
                 class="input" :class="{'input_error':!phoneFilled && phoneChange}">
        </div>
        <div class="mob-input-block" @mouseover="suggestionDisplay = true" @mouseleave="suggestionDisplay=false">
          <label for="address-input" class="label">Адрес доставки:</label>
          <input id="address-input" class="input" v-model="address"
                 placeholder="Пример: 650000, г.Кемерово, пр.Молодежный, д.1 кв.1"
                 :class="{'input_error':!addressFilled && addressChange}">
          <div class="suggestions-address" :class="{'suggestions-address_display':suggestionDisplay}">
            <div class="suggestion" v-for="(sugItem, sugKey) in suggestionDadata"
                 @click="selectedAddress(sugItem)">
              <span>{{ sugItem.unrestricted_value }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="mob-delivery" v-if="deliveryMethods">
        <div class="mob-title">Способ доставки</div>
        <div class="mob-radio-block" v-for="(deliveryItem, keyDelivery) in deliveryMethods"  :key="$root.guid()">
          <input :id="'delivery-' + deliveryItem.id" class="checkbox" type="radio" :value="deliveryItem.id"
                 v-model="deliveryMethod">
          <label :for="'delivery-' + deliveryItem.id" class="label"><span></span>{{ deliveryItem.name }}</label>
        </div>
      </div>
      <div class="mob-payments" v-if="paymentsMethods">
        <div class="mob-title">Способ оплаты</div>
        <div class="mob-radio-block" v-for="(paymentItem, keyPayment) in paymentsMethods" :key="$root.guid()">
          <input :id="'payment-' + paymentItem.id" class="checkbox" type="radio" :value="paymentItem.id"
                 v-model="paymentMethod">
          <label :for="'payment-' + paymentItem.id" class="label"><span></span>{{ paymentItem.name }}</label>
        </div>
      </div>
      <div class="mob-button">
        <div class="error" v-if="resultMakingOrder === false">Не удалось оформить заказа, попробуйте еще раз через
          несколько минут.
        </div>
        <button @click="sendOrder" class="button button_order-send" :class="{'button_loader':loaderButtonActive}"
                v-html="textButtonSendOrder"
                :disabled="!formIsFilled">отправить заказ
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import {debounce} from "lodash";
import axios from "axios";

export default {
  name: "CartMakingOrder",
  props: {},
  data: function () {
    return {
      name: '',
      nameChange: false,
      address: '',
      addressChange: false,
      mail: '',
      mailChange: false,
      phone: '',
      phoneChange: false,
      paymentMethod: '',
      deliveryMethod: '',
      //Dadata моя реализация
      suggestionDadata: {},
      suggestionDisplay: false,
      loaderButtonActive: false,
    }
  },
  beforeMount() {
    this.$store.dispatch('paymentMethods/getMethods');
    this.$store.dispatch('deliveryMethods/getMethods');
  },
  created: function () {
    this._getSuggestionsDadata = debounce(this.getSuggestionsDadata, 1000);
    this.getAddressByIp();
  },
  computed: {
    resultMakingOrder() {
      return this.$store.getters['orders/resultMakingOrder'];
    },
    deliveryMethods() {
      return this.$store.getters['deliveryMethods/methods'];
    },
    paymentsMethods() {
      return this.$store.getters['paymentMethods/methods'];
    },
    mailIsCorrect: function () {
      return !!(this.mail && this.$root.validateMail(this.mail));
    },
    nameFilled: function () {
      return this.name.length >= 3;
    },
    phoneFilled: function () {
      return this.phone.length >= 3;
    },
    addressFilled: function () {
      if (this.deliveryMethod === 1) {
        return true;
      } else {
        return this.address.length >= 3;
      }
    },
    textButtonSendOrder: function () {
      if (this.formIsFilled) {
        return '<span class="button__icon"><i class="far fa-check"></i></span><span>отправить заказ</span>';
      } else {
        return '<span class="button__icon"><i class="far fa-times"></i></span><span>заполните все поля</span>';
      }
    },
    formIsFilled: function () {
      if (this.name.length > 2 && this.addressFilled && this.mailIsCorrect && this.phone.length > 4 && this.paymentMethod && this.deliveryMethod) {
        return true;
      } else {
        return false;
      }
    },
  },
  watch: {
    name: function (newVal, oldVal) {
      this.nameChange = true;
    },
    phone: function (newVal, oldVal) {
      this.phoneChange = true;
    },
    address: function (newVal, oldVal) {
      this.addressChange = true;
      this._getSuggestionsDadata(newVal);
    },
    mail: function (newVal) {
      this.mailChange = true;
    },
  },
  methods: {
    sendDataMetrica: async function (preparedAfterOrderProduct, orderId) {
      let that = this;
      if (window.dataLayerYandex !== undefined) {
        window.dataLayerYandex.push({
          "ecommerce": {
            "currencyCode": "RUB",
            "purchase": {
              "actionField": {
                "id": orderId
              },
              "products": preparedAfterOrderProduct
            }
          }
        });
      }
    },
    getAddressByIp: async function () {
      let that = this;
      let instanceAxios = axios.create({
        baseURL: 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address',
        timeout: 1000,
      });
      instanceAxios.defaults.headers.common['Authorization'] = 'Token 7dec96e22134b4143335abdad31d3d69b6bc667c';
      instanceAxios.defaults.headers.get['Accept'] = "application/json";
      await instanceAxios.get().then(function (r) {
        if (r.data.location !== null) {
          that.address = r.data.location.value;
          that._getSuggestionsDadata.cancel();
        }
      })
    },
    getSuggestionsDadata: async function (stringAddress) {
      let that = this;
      let instance = axios.create({
        baseURL: 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address',
        timeout: 1000,
      });
      instance.defaults.headers.common['Authorization'] = 'Token 7dec96e22134b4143335abdad31d3d69b6bc667c';
      instance.defaults.headers.post['Content-Type'] = "application/json";
      instance.defaults.headers.post['Accept'] = "application/json";

      await instance.post('', {'query': stringAddress}).then(function (r) {
        console.log(r.data);
        if (r.data.suggestions.length) {
          that.suggestionDadata = r.data.suggestions;
        }
      }).catch(function (e) {
        console.log('ошибка запроса Dadata');
        console.log(e);
      });
    },
    selectedAddress: function (suggestion) {
      this.address = suggestion.unrestricted_value;
      this.suggestionDisplay = false;
    },
    sendOrder: function () {
      if (this.formIsFilled) {
        this.loaderButtonActive = true;
        let orderData = {
          customer: {
            name: this.name,
            mail: this.mail,
            phone: this.phone,
          },
          address: this.address,
          delivery: this.deliveryMethod,
          payment: this.paymentMethod,
        }
        this.$store.dispatch('orders/makingOrder', orderData);
      }
    },
  }
}
</script>

<style scoped>
.input:focus + .suggestions-address {
  display: block;
}

.suggestions-address.suggestions-address_display {
  display: block;
}

.suggestions-address {
  display: none;
  position: absolute;
  top: calc(100% - 1px);
  background: #fff;
  border-radius: 0;
  box-shadow: none;
  border: 1px solid #9E9E9E;
  /* padding: 0.25em; */
  width: max-content;
  max-height: 10rem;
  overflow-y: scroll;
}

.suggestion {
  padding: .25rem;
  cursor: pointer;
  color: #424242;
}

.suggestion:hover {
  background: rgba(243, 243, 243, 1);
  color: #1f1b20;

}
</style>