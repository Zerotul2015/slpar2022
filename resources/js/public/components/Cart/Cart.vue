<template>
  <div class="cart-page">
    <breadcrumb></breadcrumb>
    <div class="cart-content-wrap">
      <h1>Корзина</h1>
      <h2 v-if="countCart < 1">Ваша корзина пуста.
        <router-link to="/">Перейти к готовым стилевым решениям.</router-link>
      </h2>
      <div class="cart-products" v-else>
        <div class="cart-product cp-header">
          <div class="cp-number">№</div>
          <div class="cp-image">Фото</div>
          <div class="cp-name">Наименование</div>
          <div class="cp-count">Кол-во</div>
          <div class="cp-price">Цена</div>
          <div class="cp-sum">Сумма</div>
          <div class="cp-del">Удалить</div>
        </div>
        <div class="cart-product" v-for="(cartItem, key, indexKey) in cartProducts" :key="$root.guid()">
          <div class="cp-number" v-html="indexKey+1"></div>
          <img class="cp-image"
               :src="'/images/products/'+ cartItem.product.folder + '/thumb/' + cartItem.product.image_main"
               :alt="cartItem.product.name">
          <div class="cp-name" v-html="cartItem.product.name">Наименование товара</div>
          <div class="cp-count">
            <div class="cp-icon-wrap" @click="changeProductCount(cartItem.product.id, (cartItem.count - 1))">
              <icon-svg class="cp-count-icon" icon="minus" scale-x="0.7" scale-y="0.7"></icon-svg>
            </div>
            <span v-html="cartItem.count"></span>
            <div class="cp-icon-wrap" @click="changeProductCount(cartItem.product.id, (cartItem.count + 1))">
              <icon-svg class="cp-count-icon" icon="plus" scale-x="0.7" scale-y="0.7"></icon-svg>
            </div>
          </div>
          <div class="cp-price">
            <div class="cp-price-new" v-if="cartItem.product.price">
              <span class="price-to-locale">{{ cartItem.product.price | priceToLocale }}</span>
              <span class="price-currency">р.</span>
            </div>
            <div class="cp-price-old" v-if="cartItem.product.price_old">
              <span class="price-to-locale">{{ cartItem.product.price_old | priceToLocale }}</span>
              <span class="price-currency">р.</span>
            </div>
          </div>
          <div class="cp-sum">
            <div class="cp-sum-new" v-if="cartItem.product.price">
              <span class="price-to-locale">{{ (cartItem.product.price * cartItem.count) | priceToLocale }}</span>
              <span class="price-currency">р.</span>
            </div>
            <div class="cp-sum-old" v-if="cartItem.product.price_old">
              <span class="price-to-locale">{{ (cartItem.product.price_old * cartItem.count) | priceToLocale }}</span>
              <span class="price-currency">р.</span>
            </div>
          </div>
          <div class="cp-del">
            <div class="cp-icon-wrap" @click="delProduct(cartItem.product.id)">
              <icon-svg class="cp-del-icon" icon="xmark"></icon-svg>
            </div>
          </div>
        </div>
        <div class="cart-product cp-pre-footer" v-if="sumDiscountPromoCode > 0">
          <div class="cp-price cp-pre-footer-title">Промо-код <i>{{ cartPromoCodeUsed.code_text }}</i></div>
          <div class="cp-sum cp-pre-footer-val">
            <span class="price-to-locale">-{{ sumDiscountPromoCode | priceToLocale }}</span>
            <span class="price-currency">р.</span></div>
          <div class="cp-del">
          </div>
        </div>
        <div class="cart-product cp-pre-footer" v-if="sumDiscount > 0">
          <div class="cp-price cp-pre-footer-title">{{ textActiveDiscount }} <i>{{ cartPromoCodeUsed.code_text }}</i>
          </div>
          <div class="cp-sum cp-pre-footer-val">
            <span class="price-to-locale">-{{ sumDiscountProd | priceToLocale }}</span>
            <span class="price-currency">р.</span></div>
          <div class="cp-del">
          </div>
        </div>
        <div class="cart-product cp-footer">
          <div class="cp-price cp-footer-title">Итого</div>
          <div class="cp-sum cp-footer-val">
            <span class="price-to-locale">{{ cartSumWithDiscountAndPromoCode | priceToLocale }}</span>
            <span class="price-currency">р.</span></div>
          <div class="cp-del">
            <button class="btn btn_white" @click="clearCart">Очистить корзину</button>
          </div>
        </div>
        <div class="cp-promo-code-block">
          <input type="text" placeholder="промокод" minlength="2" v-model="promoCode" @keydown.enter="applyPromoCode">
          <button class="btn btn_green-invert" @click="applyPromoCode"
                  v-html="textPromoCodeBtn">Применить ПРОМО-код
          </button>
        </div>
        <div class="cp-btn-block">
          <button class="btn" @click="$router.push('/')">
            <span class="btn-text">Продолжить покупки</span>
          </button>
          <button class="btn btn_green" v-if="!isCheckoutStep" @click="isCheckoutStep = true">
            <icon-svg class="btn-icon" icon="money-check-pen"></icon-svg>
            <span class="btn-text">Перейти к оформлению</span>
          </button>
        </div>
        <cart-checkout v-if="isCheckoutStep"></cart-checkout>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "../Breadcrumb";
import IconSvg from "../Icon-svg/icon-svg";
import CartCheckout from "./CartMakingOrder";

export default {
  name: "Cart",
  components: {CartCheckout, IconSvg, Breadcrumb},
  data() {
    return {
      countPos: 1,
      promoCode: '',
      isCheckoutStep: false,
    }
  },
  computed: {
    textActiveDiscount() {
      let text = 'Дополнительная скидка на заказ';
      return text;
    },
    textPromoCodeBtn() {
      let text = 'Применить ПРОМО-код';
      if (this.resultApplyCode === true) {
        text = "ПРОМО-код активирован";
      }
      if (this.resultApplyCode === false) {
        text = "ПРОМО-код не найден";
      }
      return text;
    },
    resultApplyCode() {
      return this.$store.getters['cart/resultApplyCode'];
    },
    sumDiscountProd() { //общая скидка на товары в корзине
      let sum = 0;
      Object.keys(this.cartProducts).forEach(key => {
        if (this.cartProducts[key].product.price_old > this.cartProducts[key].product.price) {
          let difference = this.cartProducts[key].product.price_old - this.cartProducts[key].product.price;
          difference = difference * this.cartProducts[key].count;
          sum = sum + difference;
        }
      });
      return sum;
    },
    sumDiscount() { //сумма скидки за счет автоматических скидок
      let sumDiscount = 0;
      return sumDiscount;
    },
    sumDiscountPromoCode() { //скидка от использования промокода
      let sumDiscount = 0;
      //{"id":6,"date_start":"2022-04-29","date_end":"2022-05-27","code_text":"тест5","unit":"percent","amount":10}
      if (this.cartPromoCodeUsed && this.cartPromoCodeUsed.amount) {
        if (this.cartPromoCodeUsed.unit === 'percent') {
          sumDiscount = this.cartSum / 100 * this.cartPromoCodeUsed.amount;
        }
        if (this.cartPromoCodeUsed.unit === 'rub') {
          sumDiscount = this.cartPromoCodeUsed.amount;
        }
      }
      return sumDiscount;
    },
    cartSumWithDiscountAndPromoCode() { //итоговая сумма заказа с учетом всех скидок и промокода
      let sum = 0;
      if (this.cartSum) {
        sum = this.cartSum - this.sumDiscountPromoCode - this.sumDiscount;
      }
      return sum;
    },
    cartSum() {
      return this.$store.getters['cart/sum'];
    },
    countCart() {
      return this.$store.getters['cart/count'];
    },
    cartProducts() {
      return this.$store.getters['cart/products'];
    },
    cartPromoCodeUsed() {
      return this.$store.getters['cart/promoCodeUsed'];
    }
  },
  methods: {
    applyPromoCode() {
      if (this.promoCode.length > 1) {
        this.$store.dispatch('cart/applyPromoCode', this.promoCode);
      }
    },
    changeProductCount(idProduct, newCount) {
      if (idProduct && newCount > 0) {
        this.$store.dispatch("cart/changeCount", [idProduct, newCount])
      }
    },
    delProduct(idProduct) {
      if (idProduct) {
        this.$store.dispatch("cart/removeProduct", idProduct)
      }
    },
    clearCart() {
      this.$store.dispatch('cart/deleteCart');
    }
  }
}
</script>

<style scoped>

</style>