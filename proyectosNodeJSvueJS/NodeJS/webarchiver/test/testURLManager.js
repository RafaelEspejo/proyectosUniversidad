const URLManager = require('../URLManager').URLManager;
const test_getResourceExtension = 6;
const test_getDonwloadURL = 4;
pasa_getResource = 0;
pasa_getDownloadable = 0;

f_test = (() => {
    var _getResource = (test, resultado) => { 
        if (test == resultado) {
            pasa_getResource++
            return true;
        } else {
            return false;
        }
    };
    var _getDownloadable = (test, resultado) => {
        if (test == resultado) {
            pasa_getDownloadable++
            return true;
        } else {
            return false;
        }
    };
    var _passtestgetResource = () => { return pasa_getResource == test_getResourceExtension};
    var _passtestgetDownloadable = () => { return pasa_getDownloadable == test_getDonwloadURL};
    var _passtestclass = () => { return (pasa_getResource + pasa_getDownloadable) == (test_getResourceExtension + test_getDonwloadURL)};

    return {
        getResource : _getResource,
        getDownloadable : _getDownloadable,
        passtestgetResource : _passtestgetResource,
        passtestgetDownloadable : _passtestgetDownloadable,
        passtestclass : _passtestclass
    }

})();

console.log("Test de la classe URLManager", "\n")

//Test metodo getResourceExtension
console.log("Metodo getResourceExtension", "\n")

test = URLManager.getResourceExtension("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html");
console.log("URL = http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html , extension = " 
+ test + " , resultado= " + (f_test.getResource(test, ".html")? "Correcto" : "Incorrecto"), "\n")

test = URLManager.getResourceExtension("https://google.es");
console.log("URL = https://google.es , extension = " + test + " , resultado= " + (f_test.getResource(test, ".html")? "Correcto" : "Incorrecto"), "\n");

test = URLManager.getResourceExtension("http://deic-docencia.uab.cat/test/foo?a=1&b=2");
console.log("URL = http://deic-docencia.uab.cat/test/foo?a=1&b=2 , extension = " 
+ test + " , resultado= "+ (f_test.getResource(test, ".html")? "Correcto" : "Incorrecto"), "\n")

test = URLManager.getResourceExtension("http://donotexist");
console.log("URL = http://donotexist , extension = " + test + " , resultado= " + (f_test.getResource(test, ".html")? "Correcto" : "Incorrecto"), "\n")

test = URLManager.getResourceExtension("http://foo.com/a.pdf");
console.log("URL = http://foo.com/a.pdf , extension = " + test + " , resultado= " + (f_test.getResource(test, ".pdf")? "Correcto" : "Incorrecto"), "\n")

test = URLManager.getResourceExtension("http://foo.com/a");
console.log("URL = http://foo.com/a , extension = " + test + " , resultado= " + (f_test.getResource(test, ".html")? "Correcto" : "Incorrecto"))

console.log("\n" , f_test.passtestgetResource()? pasa_getResource + "/" + test_getResourceExtension 
+ " tests correctos, pasa el test del metodo getResourceExtension" : pasa_getResource + "/" + test_getResourceExtension 
+ " tests incorrectos, no pasa el test del metodo getResourceExtension", "\n")


//Test metodo getDonwloadURL
console.log("Metodo getDownloadableURL", "\n")

test = URLManager.getDownloadableURL("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html", "test_1_1.html");
 console.log("urlparent = http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html , href =  test_1_1.html , " 
 + "\nurl = " + test + " , resultado= " 
 + (f_test.getDownloadable(test, "http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test_1_1.html")? "Correcto" : "Incorrecto"))

 test = URLManager.getDownloadableURL("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html", "http://www.google.es");
 console.log("\n" ,"urlparent = http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html , href =  http://www.google.es , " 
 + "\nurl = " + test + " , resultado= " + (f_test.getDownloadable(test, "http://www.google.es/")? "Correcto" : "Incorrecto"))

 test = URLManager.getDownloadableURL("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html", "");
 console.log("\n" ,"urlparent = http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html , href =  \"\" , " 
 + "\nurl = " + test + " , resultado= "
 + (f_test.getDownloadable(test, "http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html")? "Correcto" : "Incorrecto"))

 test = URLManager.getDownloadableURL("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html", undefined);
 console.log("\n" ,"urlparent = http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html , href =  undefined , " 
 + "\nurl = " + test + " , resultado= "
 + (f_test.getDownloadable(test, "http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html")? "Correcto" : "Incorrecto"))

 console.log("\n" , f_test.passtestgetResource()? pasa_getDownloadable + "/" + test_getDonwloadURL 
 + " tests correctos, pasa el test del metodo getDownloadableURL" : pasa_getDownloadable + "/" + test_getDonwloadURL 
 + " tests incorrectos, no pasa el test del metodo getDownloadableURL")

console.log("\n" , f_test.passtestclass()? 
(pasa_getResource + pasa_getDownloadable) + "/" + (test_getResourceExtension + test_getDonwloadURL) 
+ " tests correctos, la clase URLManager pasa el test correctamente" : (pasa_getResource + pasa_getDownloadable) + "/" 
+ (test_getResourceExtension + test_getDonwloadURL) + " tests incorrectos, la clase URLManager no pasa el test correctamente") 