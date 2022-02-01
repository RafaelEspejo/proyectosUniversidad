//app con implementacion de reveling module pattern

function Tokenizer(){

    var counter = null;
    this.dictionary = [];
    this.run = llista => {
      llista.forEach(element => {
         this.dictionary[element] != undefined ? this.dictionary[element].execute() 
          : counter != null ? counter.execute() : "";
      })
    };
    this.on = (x, f) => this.dictionary[x] = f();
    this.onDefault = f => counter = f();

}


function testTokenizer(){
  
  var t = new Tokenizer();

  var contador = () => {
    var _counter = 0;
    var  _ini = () => { _counter = 0 };
    var _execute = () => { _counter++ };
    var _value = () => { return _counter }; 

    return {
      ini : _ini,
      execute : _execute,
      value : _value
    }
  };


  var countA = (t.on('a', contador)).value; 
  var countC = (t.on('c', contador)).value;
  var countOthers = (t.onDefault(contador)).value;

  var testString = ['H','o','l','a',' ','c','o','m',' ','a','n','e','u','?'];

  //here goes the code to run the test over testString
  
  t.run(testString)
  console.log("numero de a's: " + countA());
  console.log("numero de c's: " + countC());
  console.log("numero d'altres caracters: " + countOthers());
 
}

testTokenizer();