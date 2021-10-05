//
// CMSUno
// Plugin Payplug
//

UconfigNum++;

<?php $a = 0;
include(dirname(__FILE__).'/../../config.php'); // $sdata
if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/payplug.json')) {
	$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/payplug.json');
	$a = json_decode($q,true);
}
if(empty($a['ckpayplugoff'])) { ?>

jQuery(document).ready(function(){
	jQuery.post('uno/plugins/payplug/payplug.php',{'action':'load','unox':Unox},function(r){r=JSON.parse(r);
		if(r.url!=undefined)payplugUrl=r.url;
		if(r.home!=undefined)payplugHome=r.home;
		if(r.err!=undefined)payplugErr=r.err;
		if(r.par!=undefined)payplugPar=r.par;else payplugPar='lib';
	});
});
CKEDITOR.plugins.addExternal('ckpayplug',UconfigFile[UconfigNum-1]+'/../ckpayplug/');
CKEDITOR.editorConfig = function(config){
	config.extraPlugins += ',ckpayplug';
	config.toolbarGroups.push('ckpayplug');
	config.extraAllowedContent += '; a[*](payplug-btn-payment)';
	if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];
};

<?php } else { ?>

CKEDITOR.editorConfig = function(config){
	if(UconfigFile.length>UconfigNum)config.customConfig=UconfigFile[UconfigNum];
};

<?php } ?>
