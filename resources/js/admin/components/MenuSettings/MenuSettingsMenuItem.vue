<template>
  <div class="background-green pb-1">
    <div class="form-section form-section_column">
      <button class="button button_small button_remove" @click="$emit('remove-menu-item')">
        <span class="button-icon"><i class="far fa-trash-alt"></i></span>
      </button>
      <div class="input-block input-block_column input-block_highlight">
        <input type="text" v-model="title" title="Введите название пункта меню">
        <select v-model="menuItem.typeItem">
          <option v-for="(nameType, keyType) in typeMenuItem" :value="keyType">{{ nameType }}</option>
        </select>
        <div v-if="menuItem.typeItem === 'page'">
          <select v-model="menuItem.value" title="Выберите страницу">
            <option v-for="(page, pageId) in pages" :value="pageId">{{ page.title }}</option>
          </select>
        </div>
        <div v-if="menuItem.typeItem === 'pageCategory'">
          <select v-model="menuItem.value" title="Выберите категорию">
            <option v-for="(pageCat, pageCatId) in pageCategory" :value="pageCatId">{{ pageCat.name_short }}</option>
          </select>
        </div>
        <div v-if="menuItem.typeItem === 'custom'">
          <input type="text" v-model="menuItem.value" placeholder="URL" title="Введите ссылку">
        </div>
      </div>
    </div>
    <div>
      <button class="button button_small button_white" @click="addChildren">
        <span class="button-icon"><i class="far fa-sitemap"></i></span>
        <span class="button-text">добавить подпункт</span>
      </button>
      <div>
        <div class="form-section form-section_flex" v-if="children">
          <MenuSettingsMenuItem v-for="(item, keyItem) in children"
                                :key="$root.guid()"
                                :menuItem="item"
                                :type-menu-item="typeMenuItem"
                                @remove-menu-item="children.splice(keyItem, 1)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: "MenuSettingsMenuItem",
  props: {
    menuItem: {
      type: Object,
      required: true,
    },
    typeMenuItem: {
      type: Object,
      required: true,
    }
  },
  data() {
    return {
      guid: this.$root.guid(),
      title: this.menuItem.title,
      typeItem: this.menuItem.typeItem,
      value: this.menuItem.value,
      children:this.menuItem.children,
    }
  },
  created() {

  },
  watch: {
    title(newVal) {
      this.menuItem.title = newVal;
      //this.menuItem = {...this.menuItem, ...{['title']:newVal}};
    },
    typeItem(newVal) {
      this.menuItem.typeItem = newVal;
    },
    value(newVal) {
      this.menuItem.value = newVal;
    },
    children(newVal) {
      this.menuItem.children = newVal;
    },
  },
  computed: {
    pages() {
      return this.$store.state.page.allById;
    },
    pageCategory() {
      return this.$store.state.pageCategory.allById;
    }
  },
  methods: {
    removeItem(){
      this.$emit('remove-item');
    },
    addChildren() {
      if (!this.children || Object.keys(this.children).length < 1) {
        this.children = [];
      }
      this.children.push(
          {
            'title': 'Новая ссылка',
            'typeItem': 'custom',
            'value': null,
            'children': null,
          }
      );
    }
  }

}
</script>

<style scoped>

</style>