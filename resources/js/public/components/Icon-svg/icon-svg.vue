<template>
  <div v-if="componentIconName" :is="componentIconName"
       :style="{'width':iconSizeX, 'height':iconSizeY, 'fill':colorFill}"></div>
</template>

<script>
//иконки
import compareEmpty from '../../../assets/svg/compare_empty.svg';
import compareFull from '../../../assets/svg/compare_full.svg';
import search from '../../../assets/svg/search.svg';
import grid from '../../../assets/svg/grid.svg';
import grid2 from '../../../assets/svg/grid-2.svg';
import send from '../../../assets/svg/send.svg';
import cartHeader from '../../../assets/svg/cart-header.svg';
import bookmark from '../../../assets/svg/bookmark.svg';
import bookmarkSolid from '../../../assets/svg/bookmark-solid.svg';
import angleRight from '../../../assets/svg/angle-right.svg';
import angleUp from '../../../assets/svg/angle-up.svg';
import angleDown from '../../../assets/svg/angle-down.svg';
import angleLeft from '../../../assets/svg/angle-left.svg';
import anglesRight from '../../../assets/svg/angles-right.svg';
import anglesUp from '../../../assets/svg/angles-up.svg';
import anglesDown from '../../../assets/svg/angles-down.svg';
import anglesLeft from '../../../assets/svg/angles-left.svg';
import instagram from '../../../assets/svg/instagram.svg';
import trashCan from '../../../assets/svg/trash-can.svg';
import xmark from '../../../assets/svg/xmark.svg';
import xmarkLarge from '../../../assets/svg/xmark-large.svg';
import plus from '../../../assets/svg/plus.svg';
import minus from '../../../assets/svg/minus.svg';
import turnDownLeft from '../../../assets/svg/turn-down-left.svg';
import moneyCheckPen from '../../../assets/svg/money-check-pen.svg';
import cartCircleCheck from '../../../assets/svg/cart-circle-check.svg';
import cartCirclePlus from '../../../assets/svg/cart-circle-plus.svg';
import squareCheck from '../../../assets/svg/square-check.svg';
import square from '../../../assets/svg/square.svg';

//конец иконки


export default {
  name: "icon-svg",
  components: {
    //иконки
    compareEmpty,
    compareFull,
    bookmark,
    bookmarkSolid,
    cartHeader,
    grid,
    grid2,
    search,
    send,
    angleRight,
    angleLeft,
    angleUp,
    angleDown,
    anglesRight,
    anglesLeft,
    anglesUp,
    anglesDown,
    instagram,
    trashCan,
    xmark,
    xmarkLarge,
    plus,
    minus,
    turnDownLeft,
    moneyCheckPen,
    cartCircleCheck,
    cartCirclePlus,
    squareCheck,
    square,

    //конец иконки
  },
  props: {
    icon: {
      type: String,
      required: true
    },
    scaleX: {
      required: false,
    },
    scaleY: {
      required: false,
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
        'compare-full': 'compareFull',
        'compare-empty': 'compareEmpty',
        'bookmark': 'bookmark',
        'bookmark-solid': 'bookmarkSolid',
        'grid': 'grid',
        'grid-2': 'grid2',
        'search': 'search',
        'send': 'send',
        'cart-header': 'cartHeader',
        'money-check-pen': 'moneyCheckPen',
        'angle-right': 'angleRight',
        'angle-left': 'angleLeft',
        'angle-up': 'angleUp',
        'angle-down': 'angleDown',
        'angles-right': 'anglesRight',
        'angles-left': 'anglesLeft',
        'angles-up': 'anglesUp',
        'angles-down': 'anglesDown',
        'instagram': 'instagram',
        'trash-can': 'trashCan',
        'xmark-large': 'xmarkLarge',
        'xmark': 'xmark',
        'plus': 'plus',
        'minus': 'minus',
        'turn-down-left': 'turnDownLeft',
        'cart-circle-check':'cartCircleCheck',
        'cart-circle-plus':'cartCirclePlus',
        'square-check':'squareCheck',
        'square':'square',
      }
    }
  },
  created: function () {

  },
  computed: {
    colorFill() {
      return this.color ? this.color : '';
    },
    componentIconName: function () {
      if (this.iconsName[this.icon]) {
        return this.iconsName[this.icon];
      } else {
        return null;
      }
    },
    iconSizeX() {
      let size = this.iconSize;
      if (this.scaleX) {
        return this.iconSize * this.scaleX;
      }
      return size + 'px';
    },
    iconSizeY() {
      let size = this.iconSize;
      if (this.scaleY) {
        size = this.iconSize * this.scaleY;
      }
      return size + 'px';
    },
    iconSize: function () {
      if (this.size) {
        return (Math.round(parseInt(this.size) * 1.71));
      } else {
        return (Math.round(this.fontSizeDefault * 1.71));
      }
    }
  }
}
</script>

<style scoped>

</style>