<template>

  <div>

    <div class="wrapper">
     <input type="radio" name="supplier_type" id="option-1" checked v-model="supplier_type" value="ALL" :disabled="isRunSuppliers">
     <input type="radio" name="supplier_type" id="option-2" v-model="supplier_type" value="OWNER-ARTICLES" :disabled="isRunSuppliers">
       <label for="option-1" class="option option-1">
         <div class="dot"></div>
          <span>Tutti i produttori</span>
          </label>
       <label for="option-2" class="option option-2">
         <div class="dot"></div>
          <span>Produttori che gestiscono il listino articoli</span>
       </label>
    </div>

    <div v-if="supplier_type=='OWNER-ARTICLES'" class="alert alert-warning">
      Questi <b>produttori</b> gestiscono il proprio listino articoli, quindi il <b>referente</b> dovrà solo aprire e gestire l'ordine!  
    </div>

  </div>

</template>

<script>
export default {
  name: "suppliers-type",
  data() {
    return {
      supplier_type: 'ALL'
    };
  }, 
  props: ['isRunSuppliers'], 
  watch: {
      supplier_type: function() {
      /* definito in <suppliers-type @changeSupplierType="onChangeSupplierType"> */
      this.$emit('changeSupplierType', this.supplier_type); 
    }
  },  
  methods: {
    onClick() {
      /* console.log('- supplier_type '+this.supplier_type); */
      
      /* definito in <suppliers-type @changeSupplierType="onChangeSupplierType"> */
      this.$emit('changeSupplierType', this.supplier_type); 
    },
  
  }  
};
</script>

<style scoped>
.wrapper{
  display: inline-flex;
  background: #fff;
  height: auto;
  width: 100%;
  align-items: center;
  justify-content: space-evenly;
  padding: 20px 0px;
  /* box-shadow: 5px 5px 30px rgba(0,0,0,0.2); */
}
.wrapper .option{
  background: #fff;
  height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
  border-radius: 5px;
  cursor: pointer;
  padding: 0 10px;
  border: 2px solid lightgrey;
  transition: all 0.3s ease;
}
.wrapper .option .dot{
  height: 20px;
  width: 20px;
  background: #d9d9d9;
  border-radius: 50%;
  position: relative;
}
.wrapper .option .dot::before{
  position: absolute;
  content: "";
  top: 4px;
  left: 4px;
  width: 12px;
  height: 12px;
  background: #0a659e;
  border-radius: 50%;
  opacity: 0;
  transform: scale(1.5);
  transition: all 0.3s ease;
}
input[type="radio"]{
  display: none;
}
#option-1:checked:checked ~ .option-1,
#option-2:checked:checked ~ .option-2{
  border-color: #0a659e;
  background: #0a659e;
}
#option-1:checked:checked ~ .option-1 .dot,
#option-2:checked:checked ~ .option-2 .dot{
  background: #fff;
}
#option-1:checked:checked ~ .option-1 .dot::before,
#option-2:checked:checked ~ .option-2 .dot::before{
  opacity: 1;
  transform: scale(1);
}
.wrapper .option span{
  font-size: 20px;
  color: #808080;
  margin-left: 10px;
}
#option-1:checked:checked ~ .option-1 span,
#option-2:checked:checked ~ .option-2 span{
  color: #fff;
}
#option-1:hover ~ .option-1,
#option-2:hover ~ .option-2 {
  border-color: #fa824f !important;
  background: #fa824f !important;
  color: #fff;
}
.wrapper .option:hover span {
  color: #fff;
}
</style>