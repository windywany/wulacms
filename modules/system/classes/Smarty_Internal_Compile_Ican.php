<?php
class Smarty_Internal_Compile_Ican extends Smarty_Internal_CompileBase {
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $required_attributes = array ('res' );
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $optional_attributes = array ('_any' );
	
	/**
	 * Compiles code for the {ican} tag
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
		
		$res = trim ( $_attr ['res'], "\"'" );
		$type = isset ( $_attr ['type'] ) ? trim ( $_attr ['type'], "\"'" ) : '';
		
		$this->openTag ( $compiler, 'ican', array ('ican',$compiler->nocache,$res,$type ) );
		// maybe nocache because of nocache variables
		$compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
		// generate output code
		$output = "<?php ";
		if ($type) {
			$output .= " if(icando('{$res}','{$type}')){ ?>";
		} else {
			$output .= " if(icando('{$res}')){ ?>";
		}
		return $output;
	}
}

/**
 * Smarty Internal Plugin Compile ctselse Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Icanelse extends Smarty_Internal_CompileBase {
	
	/**
	 * Compiles code for the {icanelse} tag
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
		// check and get attributes
		$_attr = $this->getAttributes ( $compiler, $args );
		
		list ( $openTag, $nocache, $item, $key ) = $this->closeTag ( $compiler, array ('ican' ) );
		$this->openTag ( $compiler, 'icanelse', array ('icanelse',$nocache,$item,$key ) );
		
		return "<?php } else { ?>";
	}
}

/**
 * Smarty Internal Plugin Compile ctsclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Icanclose extends Smarty_Internal_CompileBase {
	
	/**
	 * Compiles code for the {/ican} tag
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
		list ( $openTag, $compiler->nocache, $item, $key ) = $this->closeTag ( $compiler, array ('ican','icanelse' ) );
		
		return "<?php } ?>";
	}
}
