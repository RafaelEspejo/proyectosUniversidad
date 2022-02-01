//app con implementacion con arrow function en el forEach

function Tokenizer(){

    var counter = null;
    this.dictionary = [];
    this.run = llista => {
      llista.forEach(element => {
         this.dictionary[element] != undefined ? this.dictionary[element]() 
          : counter != null ? counter() : "";
      })
    };
    this.on = (x, f) => this.dictionary[x] = f;
    this.onDefault = f => counter = f;

}

function testTokenizer(){
  
  var t = new Tokenizer();
  var countA = 0;
  var countC = 0;
  var countOthers = 0;
    
  t.on('a', () => {countA++});
  t.on('c', () => {countC++});
  t.onDefault(() => {countOthers++});

  var testString = ['H','o','l','a',' ','c','o','m',' ','a','n','e','u','?'];

  //here goes the code to run the test over testString
  
  t.run(testString)
  console.log("numero de a's: " + countA);
  console.log("numero de c's: " + countC);
  console.log("numero d'altres caracters: " + countOthers);

}

testTokenizer();