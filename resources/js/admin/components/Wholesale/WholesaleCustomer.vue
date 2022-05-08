<template>
  <div class="form">
    <h1><span v-if="item.id">Редактирование</span><span v-else>Создание</span> оптового покупателя <span v-if="item.name"
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
export default {
  name: "wholesaleCustomer"
}
</script>

<style scoped>

</style>