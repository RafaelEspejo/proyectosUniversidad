const path = require("path");
const url = require("url").URL;

class URLManager {
    static getResourceExtension = (uri) => {
        let pathname = path.extname(new url(uri).pathname);
        return pathname == "" ? ".html" : pathname;
    }

    static getDownloadableURL = (urlParent, href) => {
        if (href == "" || href == undefined) {
            return urlParent;
        } else if (href == urlParent) {
            return href;
        } else {
            return new url(href, urlParent)
        }
    }

}

module.exports = {
    URLManager
 }