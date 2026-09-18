/*
const rndMixin = {
  methods: {
    rnd() {
      let  min = Math.ceil(1);
      let max = Math.floor(100000);
      let rnd  = Math.floor(Math.random() * (max - min + 1)) + min;
      return rnd;
    }
  }
};
*/

function rndMixin() {
    let  min = Math.ceil(1);
    let max = Math.floor(100000);
    let rnd  = Math.floor(Math.random() * (max - min + 1)) + min;
    return rnd;
  }

export  { rndMixin };
