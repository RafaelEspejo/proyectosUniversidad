const http = require("http");

function GeneradorBarresDeProgres(option) {
  
  var t = null;
  var _header = ((req, res) => { res.writeHead(200, {"Content-Type": "text/plain; charset=utf-8"}); });

  var _punt = ((req, res) => { 
    console.log("Ejecutando funcion que devuelve un punto")
    _header(req, res)
    for (var i = 0; i < t; ++i) {
        (() => {
            var j = i;    
            setTimeout((() => {
                res.write('.');
                if (j == (t - 1)) {
                    res.end();
                    console.log("Cerrando la conexion.")
                }
            }), j * 1000); 
        })();
    };
});

  var _numeros = ((req, res) => {
    console.log("Ejecutando funcion que devuelve numeros.")
    _header(req, res)
    for (var i = 0; i < t; ++i) {
        (() => {
            var j = i;    
            setTimeout((() => {
                res.write(j.toString());
                if (j == (t - 1)) {
                    res.end();
                    console.log("Cerrando la conexion.")
                }
            }), j * 1000);  
        })();
    };
});

  this._novaBarra = x => {
    t = x;
    return option ? _punt : _numeros 
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
