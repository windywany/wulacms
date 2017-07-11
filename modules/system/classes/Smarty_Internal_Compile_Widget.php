<?php
/**
 * Smarty Internal Plugin Compile Cts from Cts
 *
 * Compiles the {widget} {/widget} tags
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Leo Ning
 */

/**
 * Smarty Internal Plugin Compile Cts Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Widget extends Smarty_Internal_CompileBase {
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $required_attributes = array ('widget' );
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $optional_attributes = array ('_any' );
	
	/**
	 * Compiles code for the {cts} tag
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
		// check and get attributes
		$_attr = $this->getAttributes ( $compiler, $args );		
		$type = trim ( $_attr ['widget'], "\"'" );	
		$compiler->nocache = $compiler->nocache | $compiler->tag_nocache;		
		// generate output code
		$output = "<?php ";
		$output .= '$widgetsRegister = new CustomeFieldWidgetRegister ();';
		$output .= "\n\$_wd_input_widget = \$widgetsRegister->getWidget ( '$type' );\n";
		unset ( $_attr ['var'], $_attr ['widget'] );
		$pargs = smarty_argstr ( $_attr );
		$output .= "if(\$_wd_input_widget){\n";
		$output .= "echo \$_wd_input_widget->render($pargs);";
		$output .= "\n}\n";
		$output .= "?>";		
		return $output;
	}
}
?>