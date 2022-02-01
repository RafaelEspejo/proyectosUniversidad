
//Creating the Vue object.
const rootComponent = {
  data() {
    return{
      pollingId: null,
      inbox: undefined,
      componentControl: {
        mailComposer:false,
        mailReader:false,
        mailForward:false,
        mailReply:false,
      },
      displayComponent:undefined, 
      selectedMail: null,
      addressBook: undefined,
    }
  },

  mounted: function() {
    //cuando lo montes el inbox tienes de pasarselo a mail list component con props
    //poner set interval
    this.refreshMailList()
    this.pollingId = setInterval(this.refreshMailList, 5000)
    
  },

  beforeUnmount: function() {
    clearInterval(this.pollingId)
  },
  methods: {
    sendMail: function(mail){
      fetch('/composedMail', {
        method: 'POST',
        headers:{
        'Content-Type': 'application/json',
        },
        body: JSON.stringify(mail),
        })
        .catch((error) => {
         console.error('Error:', error);
        });
    }, // end sendMail

    deleteMail: function(){
      fetch('/mail/' + this.selectedMail.id, {
        method: 'DELETE',
      }).then(response => response.json())
       .then(ajson => {this.inbox = ajson})
       .catch(err => {
       console.error(err)});
    },

    resetDisplay: function() {
      Object.keys(this.componentControl).forEach(key => this.componentControl[key]=false)
      if (this.displayComponent != null) this.componentControl[this.displayComponent] = true
    },

    refreshMailList: function(){
      fetch("/inbox")
    .then(response => response.json())
    .then(ajson => {
      this.inbox = ajson
    }).catch(err => {
      console.error(err)});
    },

    initAddressBook: function(){
      fetch("/addressBook")
    .then(response => response.json())
    .then(ajson => {
      this.addressBook = ajson
    }).catch(err => {
      console.error(err)});
    }, //end initAddressBook
  }, //end methods
  template:`<div>
            <mail-list v-bind:inbox="inbox" 
            v-on:mailcomponent="displayComponent = $event; resetDisplay()"
            v-on:refresh="refreshMailList()"
            v-on:selectedMail="selectedMail = $event; displayComponent = 'mailReader'; resetDisplay()"
            ></mail-list>

            <mail-composer v-if="componentControl['mailComposer']"
            v-on:sendMail= "sendMail($event); displayComponent = null; resetDisplay()"
            :addressbook="addressBook"
            @addressbook="initAddressBook()"
            > </mail-composer>

            <mail-reader v-bind:mail="selectedMail" 
            v-on:forward= "selectedMail = $event; displayComponent = 'mailForward'; resetDisplay()"
            v-on:reply= "selectedMail = $event; displayComponent = 'mailReply'; resetDisplay()"
            v-on:delete= "deleteMail(); displayComponent = null; resetDisplay()"
            v-if="componentControl['mailReader']"
            ></mail-reader>

            <mail-forwarder v-bind:mail="selectedMail" 
            v-if="componentControl['mailForward']"
            v-on:sendMail= "sendMail($event); displayComponent = null; resetDisplay()"
            :addressbook="addressBook"
            @addressbook="initAddressBook()"
            ></mail-forwarder>

            <mail-replier v-bind:mail="selectedMail" 
            v-if="componentControl['mailReply']"
            v-on:sendMail= "sendMail($event); displayComponent = null; resetDisplay()"
            ></mail-replier>
            </div>`
            //v-if para mostrar componentes usando la funcion resetDisplay seccion 9.4.2
} //end options
//data(){return{inboxAux: this.inbox}} recoger datos por props

//==== mail-list==============================================================
const mailListComponent = {
  name: "mail-list",
  props:['inbox'],
  emits: ['mailcomponent', 'refresh', 'selectedMail'],
  template: `<button v-on:click="$emit('mailcomponent', 'mailComposer')">Compose</button><br>
              <ul>
              <li v-for="inboxs in inbox" v-on:click="$emit('selectedMail', inboxs)">{{inboxs.from}}::{{inboxs.subject}}</li>
              </ul>
             <button v-on:click="$emit('refresh')">Refresh</button> 
            `
};
//usar emits para enviar datos al rootComponent
//=========mail-composer=======================================================
const mailComposerComponent = {
  name: "mail-composer",
  props: ['addressbook'],
  emits: ['sendMail', 'addressbook'],
  data() {
    return {
      mailComposer: { 
          to: "",
          subject: "",
          body: "",
        }
    }
  },
  template: `<form v-on:submit.prevent> 
              <input-address :addressbook="addressbook" :modelValue="mailComposer.to" @update:modelValue=" mailComposer.to = $event"
              @addressbook="$emit('addressbook')"></input-address>
              <label>Subject: <input type="text" name="subject" v-model="mailComposer.subject"></label><br>
              <label>Body: <textarea name="body" v-model="mailComposer.body"></textarea></label><br>
              <input type="submit" name="send" value="Send" v-on:click="$emit('sendMail', mailComposer)">
             </form>
            `
};

//=========mail-reader=======================================================
const mailReaderComponent = {
  name: "mail-reader",
  props: ['mail'],
  emits: ['forward', 'reply', 'delete'],
  template: `<form v-on:submit.prevent> 
              <label>From: <input type="text" name="from" v-bind:value="mail.from" readonly></label><br>
              <label>To: <input type="text" name="to" v-bind:value="mail.to" readonly></label><br>
              <label>Subject: <input type="text" name="subject" v-bind:value="mail.subject" readonly></label><br>
              <label>Body: <textarea name="body" v-bind:value="mail.body" readonly></textarea></label><br>
              <button v-on:click="$emit('forward', mail)">Forward</button>
              <button v-on:click="$emit('reply', mail)">Reply</button>
              <button v-on:click="$emit('delete')">Delete</button>
             </form>
            `
};

//=========mail-forwarder=======================================================
const mailForwarderComponent = {
  name: "mail-forwarder",
  props: ['mail', 'addressbook'],
  emits: ['sendMail','addressbook'], 
  data() {
    return {
      mailAux: {
        from: this.mail.to,
        to: "",
        subject: "Fwd: " + this.mail.subject,
        body: this.mail.body,
        id: this.mail.id,
      },
    }
  },
  template: `<form v-on:submit.prevent> 
              <label>From: <input type="text" name="from" v-bind:value="mailAux.from" readonly></label><br>
              <input-address :addressbook="addressbook" :modelValue="mailAux.to" @update:modelValue=" mailAux.to = $event"
              @addressbook="$emit('addressbook')"></input-address>
              <label>Subject: <input type="text" name="subject" v-model="mailAux.subject"></label><br>
              <label>Body: <textarea name="body" v-model="mailAux.body"></textarea></label><br>
              <input type="submit" name="send" value="Send" v-on:click="$emit('sendMail', mailAux)">
             </form>
            `
};

//=========mail-replier=======================================================
const mailReplierComponent = {
  name: "mail-replier",
  props: ['mail'],
  emits: ['sendMail'],
  data() {
    return {
      mailAux: {
        from: this.mail.to,
        to: this.mail.from,
        subject: "Re: " + this.mail.subject,
        body: this.mail.body,
        id: this.mail.id,
      },
    }
  }, 
  template: `<form v-on:submit.prevent> 
              <label>From: <input type="text" name="from" v-bind:value="mailAux.from" readonly></label><br>
              <label>To: <input type="text" name="to" v-model="mailAux.to"></label><br>
              <label>Subject: <input type="text" name="subject" v-model="mailAux.subject"></label><br>
              <label>Body: <textarea name="body" v-model="mailAux.body"></textarea></label><br>
              <input type="submit" name="send" value="Send" v-on:click="$emit('sendMail', mailAux)">
             </form>
            `
};

//=========input-address=======================================================
const inputAddress = {
  name: "input-address",
  props: ['addressbook', 'modelValue'],
  emits: ['update:modelValue'],
  data() {
    return {
      addressSelected: this.modelValue,
      display: false,
    }
  },

  methods: {
    actionButton() {
      if (!this.display) {
        this.display = true;
        this.$emit('addressbook')
      } else if (this.display) this.display = false
    },
  },
   
  template: `<label>To: <input type="text" name="to" :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)">
              <button @click="actionButton()"> Address Book </button>
              <ul v-if="display">
                <li v-for="address in addressbook" @click="addressSelected = address; $emit('update:modelValue', address); display = false" 
                >{{address}}</li>
              </ul>
              </label><br> 
            `
};

const app = Vue.createApp(rootComponent);
app.component('mail-list', mailListComponent);
app.component('mail-composer', mailComposerComponent);
app.component('mail-reader', mailReaderComponent);
app.component('mail-forwarder', mailForwarderComponent);
app.component('mail-replier', mailReplierComponent);
app.component('input-address', inputAddress);
const vm = app.mount("#app");
