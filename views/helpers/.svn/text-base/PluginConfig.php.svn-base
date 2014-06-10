<?php
class CategoryTree_View_Helper_PluginConfig extends Zend_View_Helper_Abstract
{
    public function pluginConfig($tree)
    {
		$js = array(
			WEB_PLUGIN.'/CategoryTree/views/shared/javascript/jquery.dynatree.js',
			WEB_PLUGIN.'/CategoryTree/views/shared/javascript/jquery.ui-contextmenu.js',
			WEB_PLUGIN.'/CategoryTree/views/admin/javascript/plugin_config.js',
		);
		
		$css = array(
			WEB_PLUGIN.'/CategoryTree/views/shared/javascript/skin/ui.dynatree.css',
		);
		
        $markup = <<<MARKUP
<input id='modified-tree' name='modified-tree' type='hidden' value='null'>
<p>Edit this tree by right clicking around it and pressing the save button.</p>
<div id='tree'></div>
<input id='addroot' type='button' value='Insert New Top Level Category' onclick='buttonAddRoot();'/>

<script>var categories_tree = $tree;</script>
<script type='text/javascript' src='$js[0]'></script>
<script type='text/javascript' src='$js[1]'></script>
<script type='text/javascript' src='$js[2]'></script>
<link href='$css[0]' rel='stylesheet' type='text/css'>
MARKUP;
        return $markup;
    }
}
?>
