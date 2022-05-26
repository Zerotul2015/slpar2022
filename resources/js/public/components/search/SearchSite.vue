<template>
  <div class="search-autocomplete" :class="{ [customClass_]:customClass_.length >0 }">
    <div class="search-autocomplete-input-wrap" :class="{ [customClass_]:customClass_.length >0 }">
      <label v-if="labelPosition_==='before' && labelText_.length > 0" class="search-autocomplete-label"
             :class="{ [customClass_]:customClass_.length >0 }" :for="guid">{{ labelText_ }}</label>
      <input :id="guid" class="input input_text" :class="{ [customClass_]:customClass_.length >0 }" type="text"
             :placeholder="placeholder_"
             v-model="inputVal_" @focusin="focusInput = true" @focusout="focusInput = false" autocomplete="off">
      <icon-svg class="icon-input-search" :class="{ [customClass_]:customClass_.length >0 }" v-if="iconShow_===true"
                icon="search" color=""></icon-svg>
      <label v-if="labelPosition_==='after' && labelText_.length > 0" class="search-autocomplete-label"
             :class="{ [customClass_]:customClass_.length >0 }" :for="guid">{{ labelText_ }}</label>
    </div>
    <div class="found-values" :class="{ [customClass_]:customClass_.length >0 }" v-if="foundedCount === 0"
         v-show="displayFounded" @mouseover="focusFounded = true"
         @mouseout="focusFounded = false">
      <div class="found-item found-item_not" :class="{ [customClass_]:customClass_.length >0 }">
        Совпадений не найдено
      </div>
    </div>
    <div class="found-values" :class="{ [customClass_]:customClass_.length >0 }" v-else v-show="displayFounded"
         @mouseover="focusFounded = true"
         @mouseout="focusFounded = false">
      <router-link tag="div" v-for="(foundItem, foundKey) in foundedCategory" class="found-item"
           :class="{ [customClass_]:customClass_.length >0 }" :key="$root.giud" :to="'/catalog/' + foundItem['url']">
        Категория "{{ foundItem['name'] }}"
      </router-link>
      <router-link tag="div" v-for="(foundItem, foundKey) in foundedProducts" class="found-item"
           :class="{ [customClass_]:customClass_.length >0 }" :key="$root.giud" :to="'/product/' + foundItem['url']">
        {{ foundItem['name'] }}
      </router-link>
      <router-link tag="div" v-for="(foundItem, foundKey) in foundedPages" class="found-item"
           :class="{ [customClass_]:customClass_.length >0 }" :key="$root.giud"  :to="'/page/' + foundItem['url']">
        {{ foundItem['title'] }}
      </router-link>
    </div>
  </div>
</template>

<script>

import api from "../../common/api";
import debounce from 'lodash/debounce'
import IconSvg from "../Icon-svg/icon-svg";

export default {
  name: "SearchSite",
  components: {IconSvg},
  props: {
    inputVal: { //значения для подстановки уже выбранных
      type: String,
    },
    iconShow: {
      type: Boolean,
    },
    placeholder: { // placeholder
      type: String
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
    labelPosition: { // 'before' (по умолчанию) перед input, 'after' после input
      type: String
    },
    returnKey: { // если указан то возвращает не сам объект а значение с этим ключок
      type: String
    },
    customClass: {
      type: String
    },
  },
  data: function () {
    return {
      guid: this.$root.guid(),
      inputVal_: '',
      iconShow_: false,
      placeholder_: 'поиск по сайту',
      returnKey_: false,
      columnsAll_: false,
      onlyUnique_: false,
      founded_: {},
      labelText_: '',
      labelPosition_: 'before', // два варианта before или after
      customClass_: '',
      focusInput: false,
      focusFounded: false,
    }
  },
  created: function () {
    if (this.customClass) {
      this.customClass_ = this.customClass;
    }
    if (this.inputVal) {
      this.inputVal_ = this.inputVal;
    }
    if (this.iconShow) {
      this.iconShow_ = this.iconShow;
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
    if (this.labelPosition && (this.labelPosition === 'before' || this.labelPosition === 'after')) {
      this.labelPosition_ = this.labelPosition;
    }
    if (this.labelText) {
      this.labelText_ = this.labelText;
    }
  },
  computed: {
    foundedCount() {
      let count = 0;
        if (this.foundedCategory.length > 0) {
          count = count + this.foundedCategory.length;
        }
        if (this.foundedProducts.length > 0) {
          count = count + this.foundedProducts.length;
        }
        if (this.foundedPages.length > 0) {
          count = count + this.foundedPages.length;
        }
      return count;
    },
    foundedPages() {
      return this.founded_.pages ? this.founded_.pages : [];
    },
    foundedProducts() {
      return this.founded_.products ? this.founded_.products : [];
    },
    foundedCategory() {
      return this.founded_.productsCategory ? this.founded_.productsCategory : [];
    },
    searchArray() {
      let returnArray = {};
      if (this.inputVal_.length > 1) {
        returnArray = {
          'searchString': this.inputVal_,
        }
      }
      return returnArray;
    },
    displayFounded: function () {
      return (this.inputVal_.length && (this.focusInput || this.focusFounded));
    }
  },
  watch: {
    founded_: {
      deep: true,
      handler: () => {
      }
    },
    inputVal_: function (newVal, oldVal) {
      this.searchingSite();
    }
  },
  methods: {
    selectVal: function (selectItem) {
      this.inputVal_ = selectItem;
      this.$emit('input', selectItem);
    },
    openLink: function (typeData, keyFoundVal) {
      console.log(typeData);
      let typeDataUrl = {
        'products': '/product/' + this.founded_[typeData][keyFoundVal].url,
        'productsCategory': '/catalog/' + this.founded_[typeData][keyFoundVal].url,
        'pages': '/page/' + this.founded_[typeData][keyFoundVal].url
      }
      console.log(typeDataUrl[typeData]);
      this.$router.push(typeDataUrl[typeData]);
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
    searchingSite: debounce(async function () {
      this.founded_ = {};
      let sendData = this.searchArray;
      api.getData('searchSite', sendData)
          .then((r) => {
            this.founded_ = r.returnData ? r.returnData : {};
            if (r.error) {
              this.error = 'Во время получения данных возникла ошибка: ' + r.error ? r.error : 'неизвестная ошибка';
            }
          })
          .catch((e) => {
            this.error = e.error;
          })
    }, 1500)
  }
}
</script>

<style scoped>
.search-autocomplete {
  position: relative;
}

.found-values {
  max-height: 15rem;
  overflow-y: auto;
  position: absolute;
  z-index: 1;
  left: 0;
  width: 100%;
  white-space: nowrap;
  top: 100%;
  display: flex;
  flex-direction: column;
  background: #FFFFFF;
  color: rgb(74, 74, 74);
}

.found-values::-webkit-scrollbar-track {
  border-radius: 4px;
}

.found-values::-webkit-scrollbar-thumb {
  border-radius: 4px;
}

.found-item {
  padding: .25rem;
}

.found-item:hover {
  color: rgb(79, 79, 79);
  background: rgb(237, 212, 182);
  cursor: pointer;
}

.found-item_not {
  color: #A5A5A5;
}

.found-item_not:hover {
  background: unset;
  cursor: unset;
}


</style>