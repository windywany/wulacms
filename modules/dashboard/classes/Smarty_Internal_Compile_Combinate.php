<?php
/**
 * Smarty Internal Plugin Compile Combinate
 *
 * Compiles the {combinate} tag
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Uwe Tews
 */

/**
 * Smarty Internal Plugin Compile Combinate Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Combinate extends Smarty_Internal_CompileBase {
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $shorttag_order = array ('type' );
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $optional_attributes = array ('type' );
	/**
	 * Compiles code for the {combinate} tag
	 *
	 * @param array $args
	 *        	array with attributes from parser
	 * @param Smarty_Internal_TemplateCompilerBase $compiler
	 *        	compiler object
	 * @return string compiled code
	 */
	public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler) {
		// check and get attributes
		$_attr = $this->getAttributes ( $compiler, $args );
		
		$buffer = isset ( $_attr ['type'] ) ? $_attr ['type'] : "'js'";
		
		// maybe nocache because of nocache variables
		$compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
		$this->openTag ( $compiler, 'combinate', array ($buffer,$compiler->nocache ) );
		$_output = "<?php ob_start(); ?>";
		return $_output;
	}
}

/**
 * Smarty Internal Plugin Compile Captureclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_CombinateClose extends Smarty_Internal_CompileBase {
	/**
	 * Compiles code for the {/combinate} tag
	 *
	 * @param array $args
	 *        	array with attributes from parser
	 * @param object $compiler
	 *        	compiler object
	 * @return string compiled code
	 */
	public function compile($args, $compiler) {
		// check and get attributes
		
		// must endblock be nocache?
		if ($compiler->nocache) {
			$compiler->tag_nocache = true;
		}
		
		list ( $buffer, $compiler->nocache ) = $this->closeTag ( $compiler, [ 'combinate' ] );
		
		$_output = "<?php echo(combinate_resources(ob_get_clean(),$buffer));?>";
		
		return $_output;
	}
}
