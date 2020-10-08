<template>
  <tr>
    <td data-th="Product">
      <div class="row">
        <div class="col-sm-2 d-none d-sm-block">
          <img :src="cartItem.article.img1" alt="..." class="img-fluid" />
        </div>
        <div class="col-sm-10">
          <h4 class="nomargin">{{ cartItem.article.name }}</h4>
          <p>{{ cartItem.article.descri }}</p>
        </div>
      </div>
    </td>
    <td data-th="Price">{{ cartItem.article.price }} &euro;</td>
    <td data-th="Quantity">
      <input
        type="number"
        class="form-control text-center"
        :value="cartItem.qty"
        @input="updateQuantity"
        min="0"
      />
    </td>
    <td data-th="Subtotal" class="text-center">{{ subtotal }} &euro;</td>
    <td class="actions" data-th="">
      <button class="btn btn-danger btn-sm" @click="removeItem">
        <i class="fa fa-trash-o">X</i>
      </button>
    </td>
  </tr>
</template>

<script>
import { mapActions } from "vuex";
export default {
  props: ["cartItem"],
  computed: {
    subtotal() {
      return this.cartItem.qty * this.cartItem.article.price;
    }
  },
  methods: {
    ...mapActions(["addArticle", "updateArticle", "removeItemInCart"]),
    removeItem() {

      console.log("removeItem");
      console.log(this.cartItem);      
      this.removeItemInCart(this.cartItem);
    },
    updateQuantity(event) {

      console.log("updateQuantity qty "+event.target.value);
      console.log(this.cartItem);
	    // console.log("NEW QTY event.target.value "+event.target.value);

      // this.cartItem.qty = parseInt(event.target.value);
      var cart = {
        cart: this.cartItem,
        qtyNew: parseInt(event.target.value),
        isAdd: false
      };
      this.updateArticle(cart);
    }
  }
};
</script>
