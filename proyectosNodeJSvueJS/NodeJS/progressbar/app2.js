const http = require("http");

function GeneradorBarresDeProgres(option) {
  
  var t = null;
  var _header = (req, res) => { res.writeHead(200, {"Content-Type": "text/plain; charset=utf-8"}); };
  this._novaBarra = x => {
    t = x;
    return (req, res) => {
      console.log("Ejecutando funcion que devuelve numeros.")
      _header(req, res)    
      for (let i = 0; i < t; ++i) {
        setTimeout((() => {
          res.write(option ? '.' : i.toString());
            if (i == (t-1)) {
              res.end();
              console.log("Cerrando la conexion.")
            }
        }), i * 1000); 
      }
    };
  }

  this._cambiarT = x => { t = x; }

  return {
    novaBarra : this._novaBarra,
    cambiarTiempo : this._cambiarT
  }

}

gbp = new GeneradorBarresDeProgres(true)
//gbp = new GeneradorBarresDeProgres(false)

f = gbp.novaBarra(10)

http.createServer(f).listen(8081);
console.log("Server is listening");