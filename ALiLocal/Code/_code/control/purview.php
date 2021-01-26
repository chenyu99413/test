<?php

class Control_Purview extends QUI_Control_Abstract
{

    /**
     * 自定义控件-权限
     *
     * @see QUI_Control_Abstract::render()
     */
    function render()
    {
        $checked = $this->checked;
        $contents = file_get_contents(dirname(__FILE__) . '/menu.json');
        $menus = json_decode($contents);
        $out = "<table class='FarTable'><thead><tr><th colspan='2'>权限</th></tr></thead><tbody>";
        
        $index = 0;
        foreach ($menus as $value) {
            $menu = (array) $value;
            if ($menu["children"] != null) {
                $out .= "<tr><td width='100'><label style='margin-left: 4px;'><input type='checkbox' style='margin-top: -4px;' onclick=\"SelectAll(this,'chk" . $index . "')\">" . $menu['name'] . "</label></td><td><div class='FarTool'>";
                foreach ($menu["children"] as $v) {
                    $m = (array) $v;
                    $out .= "<label class='checkbox_purview' style='margin-left: 4px;'><input type='checkbox' class='chk" . $index . "' name='purview_path[]' value='" . Control_Menu::getMenuID($m['url'], $m['param']) . "' " . (! empty($checked) && in_array(Control_Menu::getMenuID($m['url'], $m['param']), $checked) ? "checked = 'checked'" : "") . " style='margin-top: -4px;'>" . $m['name'] . "</label>";
                }
            } else {
                $out .= "<tr><td width='100'><label class='checkbox_purview' style='margin-left: 4px;'><input type='checkbox' name='purview_path[]' value='" . Control_Menu::getMenuID($menu['url'], $menu['param']) . "' " . (! empty($checked) && in_array(Control_Menu::getMenuID($menu['url'], $menu['param']), $checked) ? "checked = 'checked'" : "") . " style='margin-top: -4px;'>" . $menu['name'] . "</label></td><td><div class='FarTool'>";
            }
            $out .= "</div></td></tr>";
            $index ++;
        }
        
        $out .= "</tbody></table>";
        return $out;
    }
}