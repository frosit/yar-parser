rule md5_0105d05660329704bdb0ecd3fd3a473b {
    /*
		)){eval (${ $njap58}['q9e5e25' ])
		) ) { eval ( ${$yed7 }['
    */
    strings: $ = /\)\s*\)\s*\{\s*eval\s*\(\s*\$\{/
    condition: any of them
}
rule md5_4c4b3d4ba5bce7191a5138efa2468679 {
    strings:
        $ = "<?PHP /*** Magento** NOTICE OF LICENSE** This source file is subject to the Open Software License (OSL 3.0)* that is bundled with this package in the file LICENSE.txt.* It is also available through the world-wide-web at this URL:* http://opensource.org/licenses/osl-3.0.php**/$"
        $ = "$_SERVER['HTTP_USER_AGENT'] == 'Visbot/2.0 (+http://www.visvo.com/en/webmasters.jsp;bot@visvo.com)'"
    condition: any of them
}

rule md5_0b1bfb0bdc7e017baccd05c6af6943ea {
	/*
		eval(hnsqqh($llmkuhieq, $dbnlftqgr));?>
		eval(vW91692($v7U7N9K, $v5N9NGE));?>
    */
    strings: $ = /eval\([\w\d]+\(\$[\w\d]+, \$[\w\d]+\)\);/
    condition: any of them
}
rule md5_71a7c769e644d8cf3cf32419239212c7 {
	/*
    // $GLOBALS['ywanc2']($GLOBALS['ggbdg61']
    */
    strings: $ = /\$GLOBALS\['[\w\d]+'\]\(\$GLOBALS\['[\w\d]+'\]/
    condition: any of them
}
rule md5_825a3b2a6abbe6abcdeda64a73416b3d {
	/*
    // $ooooo00oo0000oo0oo0oo00ooo0ooo0o0o0 = gethostbyname($_SERVER["SERVER_NAME"]);
    // if(!oo00o0OOo0o00O("fsockopen"))
    // strings: $ = "$ooooo00oo0000oo0"
    */
    strings: $ = /[o0O]{3}\("fsockopen"\)/
    condition: any of them
}