rule hacked_domains {
    strings: 
        $ = "infopromo.biz"
        $ = "jquery-code.su"
        $ = "jquery-css.su"
        $ = "megalith-games.com"
        $ = "cdn-cloud.pw"
        $ = "animalzz921.pw"
    condition: any of them
}