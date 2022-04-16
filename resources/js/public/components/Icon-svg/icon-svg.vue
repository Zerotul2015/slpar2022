<template>
  <div v-if="componentIconName" :is="componentIconName" :style="{'width':iconSize, 'height':iconSize, colorFill}"></div>
</template>

<script>
//иконки
import svgAddShoppingCart from '../../../assets/svg/add_shopping_cart.svg';
import svgArrowDropDown from '../../../assets/svg/arrow_drop_down.svg';
import svgArrowRight from '../../../assets/svg/arrow_right.svg';
import svgBarChart from '../../../assets/svg/bar_chart.svg';
import svgCart from '../../../assets/svg/cart.svg';
import svgCheckBox from '../../../assets/svg/check_box.svg';
import svgCheckBoxOutlineBlank from '../../../assets/svg/check_box_outline_blank.svg';
import svgClose from '../../../assets/svg/close.svg';
import svgCompareEmpty from '../../../assets/svg/compare_empty.svg';
import svgCompareFull from '../../../assets/svg/compare_full.svg';
import svgFavorite from '../../../assets/svg/favorite.svg';
import svgGrid from '../../../assets/svg/grid.svg';
import svgHeart from '../../../assets/svg/heart.svg';
import svgHeartOutline from '../../../assets/svg/heart-outline.svg';
import svgHome from '../../../assets/svg/home.svg';
import svgIndeterminateCheckBox from '../../../assets/svg/indeterminate_check_box.svg';
import svgMenu from '../../../assets/svg/menu.svg';
import svgPhone from '../../../assets/svg/phone.svg';
import svgRadioButtonChecked from '../../../assets/svg/radio_button_checked.svg';
import svgRadioButtonUnchecked from '../../../assets/svg/radio_button_unchecked.svg';
import svgSearch from '../../../assets/svg/search.svg';
import svgSend from '../../../assets/svg/send.svg';
import svgShoppingCart from '../../../assets/svg/shopping_cart.svg';
import svgSort from '../../../assets/svg/sort.svg';
import svgViewSequential from '../../../assets/svg/view_sequential.svg';
//конец иконки


export default {
  name: "icon-svg",
  components: {
    //иконки
    svgAddShoppingCart,
    svgArrowDropDown,
    svgArrowRight,
    svgBarChart,
    svgCart,
    svgCheckBox,
    svgCheckBoxOutlineBlank,
    svgClose,
    svgCompareEmpty,
    svgCompareFull,
    svgFavorite,
    svgGrid,
    svgHeart,
    svgHeartOutline,
    svgHome,
    svgIndeterminateCheckBox,
    svgMenu,
    svgPhone,
    svgRadioButtonChecked,
    svgRadioButtonUnchecked,
    svgSearch,
    svgSend,
    svgShoppingCart,
    svgSort,
    svgViewSequential,
    //конец иконки
  },
  props: {
    icon: {
      type: String,
      required: true
    },
    size: {},
    color: {
      type: String,
      required: false
    }
  },
  data: function () {
    return {
      fontSizeDefault: 14,
      iconsName: {
        'svg-add-shopping-cart': 'svgAddShoppingCart',
        'svg-arrow-drop-down': 'svgArrowDropDown',
        'svg-arrow-right': 'svgArrowRight',
        'svg-bar-chart': 'svgBarChart',
        'svg-cart': 'svgCart',
        'svg-check-box': 'svgCheckBox',
        'svg-check-box-outline-blank': 'svgCheckBoxOutlineBlank',
        'svg-close': 'svgClose',
        'svg-compare-empty': 'svgCompareEmpty',
        'svg-compare-full': 'svgCompareFull',
        'svg-favorite': 'svgFavorite',
        'svg-grid': 'svgGrid',
        'svg-heart': 'svgHeart',
        'svg-heart-outline': 'svgHeartOutline',
        'svg-home': 'svgHome',
        'svg-indeterminate-check-box': 'svgIndeterminateCheckBox',
        'svg-menu': 'svgMenu',
        'svg-phone': 'svgPhone',
        'svg-radio-button-checked': 'svgRadioButtonChecked',
        'svg-radio-button-unchecked': 'svgRadioButtonUnchecked',
        'svg-search': 'svgSearch',
        'svg-send': 'svgSend',
        'svg-shopping-cart': 'svgShoppingCart',
        'svg-sort': 'svgSort',
        'svg-view-sequential': 'svgViewSequential',
      }
    }
  },
  created: function () {

  },
  computed: {
    colorFill() {
      return this.color ? 'fill:' + this.color : '';
    },
    componentIconName: function () {
      if (this.iconsName[this.icon]) {
        return this.iconsName[this.icon];
      } else {
        return null;
      }
    },
    iconSize: function () {
      if (this.size) {
        return (Math.round(parseInt(this.size) * 1.71)) + 'px';
      } else {
        return (Math.round(this.fontSizeDefault * 1.71)) + 'px';
      }
    }
  }
}
</script>

<style scoped>

</style>