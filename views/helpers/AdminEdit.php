<?php
class CategoryTree_View_Helper_AdminEdit extends Zend_View_Helper_Abstract
{
    public function adminEdit($tree)
    {
		$js = array(
			WEB_PLUGIN.'/CategoryTree/views/shared/javascript/jquery.dynatree.js',
			WEB_PLUGIN.'/CategoryTree/views/shared/javascript/jquery.ui-contextmenu.js',
			WEB_PLUGIN.'/CategoryTree/views/admin/javascript/edit_tab.js',
		);
		
		$css = array(
			WEB_PLUGIN.'/CategoryTree/views/shared/javascript/skin/ui.dynatree.css',
		);
		
        $markup = <<<MARKUP
<input id='selCats' name='selCats' type='hidden' value='poop'>
<div id='categories'></div>
<div id='tree'></div>

<script>var categories_tree = $tree; </script>
<script type='text/javascript' src='$js[0]'></script>
<script type='text/javascript' src='$js[1]'></script>
<script type='text/javascript' src='$js[2]'></script>
<link href='$css[0]' rel='stylesheet' type='text/css'>
MARKUP;
        return $markup;
    }
}
?>
