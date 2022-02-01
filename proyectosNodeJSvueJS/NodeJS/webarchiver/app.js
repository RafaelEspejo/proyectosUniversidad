const express = require('express');
const fetch = require('node-fetch');
const cheerio = require ('cheerio');
const stream = require('stream');
const path = require('path');
const archiver = require('archiver');
const URLManager = require('./URLManager').URLManager;
const ReplaceManager = require('./ReplaceManager').ReplaceManager;


function getTransformStream(url, recLevel, replaceManager, doCrawlAndDownloadResource) {
  let transformStream = new stream.Transform();
  let buffer='';

  transformStream._transform = function(chunk, encoding, callback) {
    buffer += chunk.toString();
    callback();
  };

  transformStream._flush = function(callback){
    this.push(transformStream._replace(buffer));
    callback();
  }

  transformStream._replace = function(chunk){
      $ = cheerio.load(chunk);
      $('a').each(function (i, link){
        let href = $(this).attr('href');
        let downloadableURL = URLManager.getDownloadableURL(url,href);
        let newhref = replaceManager.lookupName(downloadableURL);
        $(this).attr('href', newhref);

        doCrawlAndDownloadResource(downloadableURL, recLevel - 1, newhref);

      }); //end $a.each
      return $.html();
  };

  return transformStream;
}//end getTransformStream

//TODO function function URLManager()

//TODO function ReplaceManager(maxFiles)

function startCrawling(req, res){
  let downloadedFiles = [];
  let url = req.query.uri;
  let reclevel = req.query.reclevel;
  let maxfiles = req.query.maxfiles;
  let replace = new ReplaceManager(maxfiles);
  //res.writeHead(200, {'Content-Type': 'text/html'});
  let count = 0;
  

  nameZip = 'files.zip'
  res.writeHead(200, {'Content-Type': 'application/zip',
  'Content-Disposition' : 'attachment; filename=' + nameZip })
  zip = archiver('zip')
  zip.on('finish', () => {
    console.log("Zip creado con exito y enviado")
    res.end();
  }).pipe(res);

  let doCrawlAndDownloadResource = (url, recLevel, name) => {
     
      /*fetch(url).then(response => {
       response.body.pipe(res)
       .on('finish', () => res.end());
      })*/
      if (recLevel > 0) {
        if (downloadedFiles[name] != name && name != "404.html") {
          downloadedFiles[name]=name;
          fetch(url).then(response => {
            r = getTransformStream(url, recLevel, replace, doCrawlAndDownloadResource)
            response.body.pipe(r);             
            return r;   
          }).then( resp => {     
            zip.append(resp, {name: name})
            return resp;
          }).then(x => {
            x.on('finish', () => {
              count++;
              if (count == Object.keys(downloadedFiles).length) {
                zip.finalize();
              }
            })
          });
        }
      }
  };

  doCrawlAndDownloadResource(url, reclevel, replace.lookupName(url));
  
}

const app = express()
const port = 3000


app.use(express.static(path.join(__dirname, 'public')));

//here goes the routing 

app.get('/crawler', (req, res) => {
  startCrawling(req, res);
})

app.listen(port, () => console.log(`Example app listening on port ${port}!`));
