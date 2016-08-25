<?php
class PrettyhtmlHookImpl {
	public static function on_load_dashboard_js_file($html) {
		$js = MODULE_URL . 'prettyhtml/pretty.js';
		$html .= '<script type="text/javascript" src="' . $js . '"></script>';
		return $html;
	}
	public static function on_load_dashboard_css($html) {
		$html .= '.ke-icon-pretty { background-position: 0 -1056px; height: 16px; width: 16px; } .ke-pretty .checkbox-inline input {margin-left: 0;position: inherit;} .smart-style-5 .checkbox-inline{color:#666}';
		return $html;
	}
	public static function on_load_editor_css($css) {
		$css .= '.ke-content img{max-width:760px; height:auto}';
		return $css;
	}
	public static function get_editor_plugins($plugins) {
		$plugins [] = 'pretty';
		return $plugins;
	}
	public static function get_editor_layout($layout) {
		$aaa = '<label class="checkbox-inline"><input type="checkbox" class="keepTable" checked="checked"/> 保留表格</label>';
		$aaa .= '<label class="checkbox-inline"><input type="checkbox" class="keepList" checked="checked"/> 保留列表 </label>';
		$aaa .= '<label class="checkbox-inline"><input type="checkbox" class="keepStrong" checked="checked"/> 保留Strong </label>';
		$aaa .= '<label class="checkbox-inline"><input type="checkbox" class="keepHead" checked="checked"/> 保留H1-H6 </label>';
		$aaa .= '<label class="checkbox-inline"><input type="checkbox" class="borderImg"/> 图片加边框 </label>';
		$aaa .= '<label class="checkbox-inline"><input type="checkbox" class="addBlank" checked="checked"/> 段前加空格 </label>';
		return '<div class="container"><div class="toolbar"></div><div class="ke-pretty ke-toolbar">' . $aaa . '</div><div class="edit"></div><div class="statusbar"></div></div>';
	}
}
