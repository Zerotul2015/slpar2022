<template>
  <div class="input-element input-element_autocompletion">
    <label class="input-element__label input-element__label_autocompletion" :for="inputId_">{{ labelText_ }}:</label>
    <input :id="inputId_" class="input input-element__input" type="text"
           v-model="inputVal_" @focusin="focusInput = true" @focusout="focusInput = false" autocomplete="off">
    <div class="found-values" v-show="displayFounded" @mouseover="focusFounded = true" @mouseout="focusFounded = false">
      <div v-for="(foundItem, foundKey) in founded_" class="found-item" @click="selectVal(foundItem)">
        {{ foundItem[columnName] }}<span v-if="pageDetails" class="link-external" @click.prevent="openLink(foundKey)"
                                         title="открыть в новой вкладке"><i class="far fa-external-link"></i></span>
      </div>
      <div class="found-item found-item_not" v-if="founded_.length < 1">
        Совпадений не найдено
      </div>
    </div>
  </div>
</template>

<script>

import axios from 'axios'
import debounce from 'lodash/debounce'

export default {
  name: "search-auto-complete",
  props: {
    inputId: { // уникальнй ключ для поле если на старнице несколько экземпляров компонента
      type: String,
    },
    inputVal: { //значения для подстановки уже выбранных
      type: String,
    },
    onlyUnique: { // только уникальные значения поля имя казывается в uniqueKey(по умолчанию id)
      type: Boolean
    },
    uniqueKey: { //имя поля по которому отбирать уникльные значения (по умолчанию id)
      type: String
    },
    labelText: { // Текст для label
      type: String,
    },
    urlGet: {// ссылка на обработчик запроса
      type: String,
      required: true
    },
    columnName: {//имя поля в котором искать совпадения
      type: String,
      required: true
    },
    pageDetails: { // часть ссылки для открытия страницы с найденым к нему в конце добавляется id(/admin/products/{{pageDetails}})
      type: String
    },
    returnKey: { // если указан то возвращает не сам объект а значение с этим ключок
      type: String
    },
    columnsAll: { // запрашивать все поля, а не только то по которому идет описк
      type: Boolean
    }
  },
  data: function () {
    return {
      inputId_: 'input' + this.key,
      inputVal_: '',
      returnKey_: false,
      columnsAll_: false,
      onlyUnique_: false,
      founded_: [],
      labelText_: '',
      focusInput: false,
      focusFounded: false,
    }
  },
  created: function () {
    this.searchValDebounced = debounce(this.searchVal, 500, {maxWait: 1500});
    if (this.inputId) {
      this.inputId_ = this.inputId;
    }
    if (this.inputVal) {
      this.inputVal_ = this.inputVal;
    }
    if (this.returnKey) {
      this.returnKey_ = this.returnKey;
    }
    if (this.columnsAll !== undefined) {
      this.columnsAll_ = this.columnsAll;
    }
    if (this.onlyUnique !== undefined) {
      this.onlyUnique_ = this.onlyUnique;
    }
    if (this.labelText) {
      this.labelText_ = this.labelText;
    }
  },
  computed: {
    displayFounded: function () {
      return (this.inputVal_.length && (this.focusInput || this.focusFounded));
    }
  },
  watch: {
    inputVal_: function (newVal, oldVal) {
      this.searchValDebounced();
    }
  },
  methods: {
    selectVal:function(selectItem){
      this.inputVal_=selectItem[this.columnName];
      if(this.returnKey_){
        this.$emit('input', selectItem[this.returnKey_]);
      }else{
        this.$emit('input', selectItem);
      }

    },
    openLink: function (keyFoundVal) {
      console.log('openLink');
      if (this.pageDetails) {
        window.open(this.pageDetails + '/' + this.founded_[keyFoundVal].id, '_blank');
      }
    },
    filterUnique: function (arrayVal) {
      if (this.onlyUnique_) {
        let keyFilter = 'id';
        if (this.uniqueKey) {
          keyFilter = this.uniqueKey
        }

        let arrayReturned = [];
        let arrayValFiltered = {}
        this.founded_.forEach(valItem => {
          console.log(valItem);
          if (!arrayValFiltered[valItem[keyFilter]]) {
            arrayValFiltered[valItem[keyFilter]] = valItem;
            arrayReturned.push(valItem);
          }
        });
        this.founded_ = arrayReturned;
      }
    },
    searchVal: async function () {
      let that = this;
      let searchString = this.inputVal_;
      console.log('ищем ' + searchString + ' в ' + that.columnName);
      await axios.post(that.urlGet, {'where': that.columnName, 'searchString': searchString, 'all':that.columnsAll_})
          .then(r => {
            if (r.data.result && r.data.result === true && r.data.returnData) {
              that.founded_ = r.data.returnData;
              that.filterUnique();
            } else {
              that.founded_ = [];
            }
          })
          .catch(e => {
            that.founded_ = [];
          });
    }
  }
}
</script>

<style scoped>
.input-element_autocompletion {
  position: relative;
}

.found-values {
  max-height: 15rem;
  overflow-y: auto;
  position: absolute;
  z-index: 1;
  left: 0;
  right: 0;
  top: 100%;
  display: flex;
  flex-direction: column;
  background: #FFFFFF;
  color: #000;
  border: 1px solid #A5A5A5;
  border-radius: 5px;
}

.found-values::-webkit-scrollbar-track {
  border-radius: 5px;
}

.found-values::-webkit-scrollbar-thumb {
  border-radius: 5px;
}

.found-item {
  padding: .25rem;
  margin: .25rem 0;
}

.found-item:hover {
  background: #dddddd;
  color: #0e1313;
  cursor: pointer;
}

.found-item_not {
  color: #A5A5A5;
}

.found-item_not:hover {
  background: unset;
  color: #A5A5A5;
  cursor: unset;
}

.link-external {
  margin-left: 1rem;
  cursor: pointer;
  color: #fff;
}

.link-external:hover {
  color: #f80000;
}

.found-item:hover .link-external {
  color: #ffffff;
}

.found-item:hover .link-external:hover {
  color: #ffffff;
}
</style>