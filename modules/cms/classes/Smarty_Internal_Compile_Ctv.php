<?php
/**
 * Smarty Internal Plugin Compile Ctv from Foreach
 *
 * Compiles the {ctv} tags
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Leo Ning
 */

/**
 * Smarty Internal Plugin Compile Ctv Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Ctv extends Smarty_Internal_CompileBase {
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $required_attributes = array ('var', 'from' );
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $optional_attributes = array ('_any' );
	
	/**
	 * Compiles code for the {cta} tag
	 *
	 * @param array  $args      array with attributes from parser
	 * @param object $compiler  compiler object
	 * @param array  $parameter array with compilation parameter
	 * @return string compiled code
	 */
	public function compile($args, $compiler, $parameter) {
		$tpl = $compiler->template;
		// check and get attributes
		$_attr = $this->getAttributes ( $compiler, $args );
		
		$name = $_attr ['var'];
		$sink = trim ( $_attr ['from'], '\'"' );
		$item = $name;
		$pname = trim ( $name, '\'"' );
		$this->openTag ( $compiler, 'ctv', array ('ctv', $compiler->nocache, $name, $sink ) );
		// maybe nocache because of nocache variables
		$compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
		// generate output code
		$output = "<?php ";
		$output .= " \$_smarty_tpl->tpl_vars[$item] = new Smarty_Variable;\n";				
		unset ( $_attr ['var'], $_attr ['from']);
		$pargs = smarty_argstr ( $_attr );
        $output .= " \$_{$pname}_data = get_data_from_cts_provider('$sink', $pargs,\$_smarty_tpl->tpl_vars);\n";
		$output .= " \$_smarty_tpl->tpl_vars[$item]->value = \$_{$pname}_data->getData();\n";
		$output .= " if (\$_smarty_tpl->tpl_vars[$item]->value){\n";
		$output .= "?>";
		return $output;
	}
}
/**
 * Smarty Internal Plugin Compile ctvelse Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Ctvelse extends Smarty_Internal_CompileBase {
	
	/**
	 * Compiles code for the {ctvelse} tag
	 *
	 * @param array  $args array with attributes from parser
	 * @param object $compiler compiler object
	 * @param array  $parameter array with compilation parameter
	 * @return string compiled code
	 */
	public function compile($args, $compiler, $parameter) {
		list ( $openTag, $nocache, $item, $key ) = $this->closeTag ( $compiler, array ('ctv' ) );
		$this->openTag ( $compiler, 'ctvelse', array ('ctvelse', $nocache, $item, $key ) );		
		return "<?php } else { ?>";
	}

}

/**
 * Smarty Internal Plugin Compile ctvclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Ctvclose extends Smarty_Internal_CompileBase {
	
	/**
	 * Compiles code for the {/ctv} tag
	 *
	 * @param array  $args      array with attributes from parser
	 * @param object $compiler  compiler object
	 * @param array  $parameter array with compilation parameter
	 * @return string compiled code
	 */
	public function compile($args, $compiler, $parameter) {		
		// must endblock be nocache?
		if ($compiler->nocache) {
			$compiler->tag_nocache = true;
		}
		list ( $openTag, $compiler->nocache, $item, $key ) = $this->closeTag ( $compiler, array ('ctv', 'ctvelse' ) );		
		return "<?php } ?>";
	}
}
?>