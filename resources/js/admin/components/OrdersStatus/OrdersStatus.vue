<template>
  <div class="form">
    <h1>Статусы заказов</h1>
    <pre class="help">
      При формировании текста письма используются автоматическая подстановка значений:
      [id] - номер заказа. Пример 15
      [link.details] - ссылка на просмотр заказа. Пример: https://site.ru/order/preview-order/
      [link.pay] - ссылка на оплату заказа. Пример: https://site.ru/order/pay/15
      [name] - имя покупателя(название организации). Пример: Иванов Петр(ООО "Рога и копыта")
    </pre>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <orders-status-item v-for="(itemCurrent) in items" :item="itemCurrent"></orders-status-item>
  </div>
</template>

<script>
import api from "../../common/api";
import OrdersStatusItem from "./OrdersStatusItem";

export default {
  name: "OrdersStatus",
  components: {OrdersStatusItem},
  data() {
    return {
      loading: false,
      error: null,
      items: [],
    }
  },
  beforeMount() {
    this.getItems();
  },
  methods: {
    getItems: async function () {
      this.loading = true;
      api.getData('ordersStatus', )
          .then((r) => {
            this.loading = false;
            if (r.result === true) {
              this.items = r.returnData;
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e.error ? e.error : 'неизвестная ошибка: ' + e;
          })
    }
  }

}
</script>

<style scoped>
pre.help {
  background: #f5f5f5;
  width: fit-content;
  padding: 0.5rem;
  border-radius: 5px;
}
</style>