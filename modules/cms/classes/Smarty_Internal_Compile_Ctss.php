<?php
/*
 * Smarty Internal Plugin Compile Ctss for cts Compiles the {ctss} and {/ctss} tags
 */
class Smarty_Internal_Compile_Ctss extends Smarty_Internal_CompileBase {
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $required_attributes = array ('var' );
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $optional_attributes = array ('_any' );
	
	/**
	 * Compiles code for the {ctss} tag
	 *
	 * @param array $args
	 *        	array with attributes from parser
	 * @param object $compiler
	 *        	compiler object
	 * @param array $parameter
	 *        	array with compilation parameter
	 * @return string compiled code
	 */
	public function compile($args, $compiler, $parameter) {
		$tpl = $compiler->template;
		$_attr = $this->getAttributes ( $compiler, $args );
		$name = $_attr ['var'];
		$item = $name;
		$pname = trim ( $name, '\'"' );
		if (isset ( $_attr ['model'] )) {
			$sink = trim ( $_attr ['model'], "\"'" );
		} else {
			$sink = '';
		}
		$this->openTag ( $compiler, 'ctss', array ('ctss',$compiler->nocache,$name,$sink ) );
		$compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
		unset ( $_attr ['var'] );
		$pargs = smarty_argstr ( $_attr );
		$output = "<?php ";
		$output .= " \$_smarty_tpl->tpl_vars[$item] = new Smarty_Variable;\n";
		$output .= " \$_smarty_tpl->tpl_vars[$item]->value = new CmsPageSearcher();\n";
		$output .= " \$_{$pname}_data = \$_smarty_tpl->tpl_vars[$item]->value->doSearch($pargs);\n";
		$output .= "?>";
		return $output;
	}
}
class Smarty_Internal_Compile_Ctssclose extends Smarty_Internal_CompileBase {
	/**
	 * Compiles code for the {/ctss} tag
	 *
	 * @param array $args
	 *        	array with attributes from parser
	 * @param object $compiler
	 *        	compiler object
	 * @param array $parameter
	 *        	array with compilation parameter
	 * @return string compiled code
	 */
	public function compile($args, $compiler, $parameter) {
		// must endblock be nocache?
		if ($compiler->nocache) {
			$compiler->tag_nocache = true;
		}
		list ( $openTag, $compiler->nocache, $item, $sink ) = $this->closeTag ( $compiler, array ('ctss' ) );
		return '';
	}
}