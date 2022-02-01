function ReplaceManager(maxFiles) {
  var _fileCounter = 0;
  var _replaceMap = [];
  var _NOT_FOUND_FILE = "404.html";

  this.lookupName = (_url) => {
        if (_fileCounter == 0) {
            _replaceMap[_url] = "index.html"
            _fileCounter++;
            return "index.html";
        } else {
            if (_replaceMap[_url]){
                return _replaceMap[_url];
            } else {
                if (_fileCounter < maxFiles) {
                    var value = _fileCounter + (require('./URLManager').URLManager.getResourceExtension(_url)); 
                    _replaceMap[_url] = value;
                    _fileCounter++;
                    return value;    
                } else if (_fileCounter == maxFiles) {
                    _replaceMap[_url] = _NOT_FOUND_FILE;
                    return _NOT_FOUND_FILE;
                }
            }
        }
    }
}

module.exports={
    ReplaceManager
}