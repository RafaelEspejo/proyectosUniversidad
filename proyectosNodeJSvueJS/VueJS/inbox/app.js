const express = require('express');
const session = require('express-session');
const path = require('path');
const userDB = require("./model/userDB").userDB;
const Mail = require("./model/mailServer").Mail;
const mailServer = require("./model/mailServer").mailServer;

const app = express()
const port = 3000

app.use(express.static(path.join(__dirname,'public')));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(session({
  secret: 'keyboard cat',
  resave: false,
  saveUninitialized: true}));

app.use((req, res, next) => {
  if(!req.session.account){
    if(req.path == '/login') {
      console.log("applying next for login");
      next();
    }else if(req.path == '/'){
      console.log("redirecting to login.html");
      res.redirect('/login.html');
    }else{
      console.log("Illegal access");
      res.status(500).end('Operation not permitted');
    }
  }else {
    next();
  }
})

app.post("/login", (req, res) => {
  let account = req.body.account;
  let password = req.body.password;
  if(userDB[account] !== undefined && password == userDB[account]){
    req.session.account = account;
    res.redirect('/');
    console.log("Usuario Ok. Redirigiendo hacia el inbox")
  }else{
    let accountDoesNotExist = 1;
    let passwdIncorrect = 2;
    let errCode = (userDB[account] === undefined ? accountDoesNotExist : passwdIncorrect);
    res.redirect(`/login.html?error=${errCode}`);
    console.log("Usuario NOOk. Redirigiendo hacia pagina error")
  }
});

app.get("/", (req, res) =>{
  res.sendFile(path.join(__dirname,'index.html'));
})

app.get("/inbox", (req, res) =>{
  //console.log("Pidiendo el inbox")
  res.json(mailServer.getInbox(req.session.account))
})

app.post("/composedMail", (req, res) => {
  mailServer.addMail(new Mail(req.session.account, req.body.to, req.body.subject, req.body.body));
  res.status(200).end()
})

app.get("/addressBook", (req, res) => {
  res.json(mailServer.getAddressBook())
})

app.delete("/mail/:mailId", (req, res) => {
  mailServer.deleteMail(req.session.account, req.params.mailId)
  res.json(mailServer.getInbox(req.session.account))
})
//Begin routing
//al programar el get inbox el mailadress esta en la session.account
//creating an HTTP server.
app.listen(port, () => console.log(`Example app listening on port ${port}!`))
