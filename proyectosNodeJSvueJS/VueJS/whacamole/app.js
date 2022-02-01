// This is the main instance
const rootComponent = {
  template: `<div style="display: flex; flex-wrap: wrap">
   <mole></mole><mole></mole><mole></mole>
   <mole></mole><mole></mole><mole></mole>
  </div>`,
};
const moleComponent = {
  //TODO
  data() {
    return {
      image:"empty.png"
    }
  },
  mounted() {
   let game = () => {
        setTimeout(() => {
          if (this.image != "ouch.png") {
            if (this.image == "empty.png") this.image = "mole.png"
            else if (this.image == "mole.png") this.image = "empty.png"
            game()
          }
        }, Math.random()* 1000 + 500)
    }
    game()
  },
  methods: {
    final: function() {
      if (this.image == "mole.png") {
         this.image = "ouch.png"
         console.log("Bien hecho")
      }else {
        console.log("Fallaste")
      }
    }
  },
  template: `<div class="moleHole"><img v-bind:src="image" v-on:click="final"></div> `,
  
};
const app = Vue.createApp(rootComponent);
app.component('mole', moleComponent);
const vm = app.mount("#app");
