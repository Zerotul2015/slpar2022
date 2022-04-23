<template>
  <div v-if="componentIconName" :is="componentIconName" :style="{'width':iconSize, 'height':iconSize, colorFill}"></div>
</template>

<script>
//иконки
import search from '../../../assets/svg/search.svg';
import grid from '../../../assets/svg/grid.svg';
import grid2 from '../../../assets/svg/grid-2.svg';
import send from '../../../assets/svg/send.svg';
import cartShopping from '../../../assets/svg/cart-shopping.svg';
import cartPlus from '../../../assets/svg/cart-plus.svg';
import cartMinus from '../../../assets/svg/cart-minus.svg';
import angleRight from '../../../assets/svg/angle-right.svg';
import angleUp from '../../../assets/svg/angle-up.svg';
import angleDown from '../../../assets/svg/angle-down.svg';
import angleLeft from '../../../assets/svg/angle-left.svg';
import anglesRight from '../../../assets/svg/angles-right.svg';
import anglesUp from '../../../assets/svg/angles-up.svg';
import anglesDown from '../../../assets/svg/angles-down.svg';
import anglesLeft from '../../../assets/svg/angles-left.svg';

//конец иконки


export default {
  name: "icon-svg",
  components: {
    //иконки
    cartShopping,
    cartPlus,
    cartMinus,
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
        'grid': 'grid',
        'grid-2': 'grid2',
        'search': 'search',
        'send': 'send',
        'cart-shopping': 'cartShopping',
        'cart-plus': 'cartPlus',
        'cart-minus': 'cartMinus',
        'angle-right': 'angleRight',
        'angle-left': 'angleLeft',
        'angle-up': 'angleUp',
        'angle-down': 'angleDown',
        'angles-right': 'anglesRight',
        'angles-left': 'anglesLeft',
        'angles-up': 'anglesUp',
        'angles-down': 'anglesDown',
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