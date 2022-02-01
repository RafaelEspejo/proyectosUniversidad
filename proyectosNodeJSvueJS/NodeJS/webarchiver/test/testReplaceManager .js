const ReplaceManager = require('../ReplaceManager').ReplaceManager;


var r = new ReplaceManager(4);

console.log(r.lookupName("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test.html"))
console.log(r.lookupName("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test_1_1.html"))
console.log(r.lookupName("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test_1_2.pdf"))
console.log(r.lookupName("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test_1_2.pdf"))
console.log(r.lookupName("http://stw.deic-docencia.uab.cat/nodeJS/webArchiver/test_1_3.html"))