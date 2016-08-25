<?php
/**
 * Smarty Internal Plugin Compile Cts from Cts
 *
 * Compiles the {cts} {ctselse} {/cts} tags
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
class Smarty_Internal_Compile_Cts extends Smarty_Internal_CompileBase {
	/**
	 * Attribute definition: Overwrites base class.
	 *
	 * @var array
	 * @see Smarty_Internal_CompileBase
	 */
	public $required_attributes = array ('var','from' );
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
		
		$name = $_attr ['var'];
		$sink = trim ( $_attr ['from'], "\"'" );
		if (! strncmp ( "\$_smarty_tpl->tpl_vars[$name]", $sink, strlen ( $name ) + 24 )) {
			$compiler->trigger_template_error ( "var variable {$name} may not be the same variable as at 'from'", $compiler->lex->taglineno );
		}
		$item = $name;
		$pname = trim ( $name, '\'"' );
		$loop = true;
		if (isset ( $_attr ['loop'] )) {
			$loop = trim ( $_attr ['loop'], '\'"' );
			if ($loop === 'false') {
				$loop = false;
			}
		}
		
		$this->openTag ( $compiler, 'cts', array ('cts',$compiler->nocache,$name,$sink,$loop ) );
		// maybe nocache because of nocache variables
		$compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
		// generate output code
		$output = "<?php ";
		$output .= " \$_smarty_tpl->tpl_vars[$item] = new Smarty_Variable;\$_smarty_tpl->tpl_vars[$item]->_loop = false;\n";
		unset ( $_attr ['var'], $_attr ['from'], $_attr ['loop'] );
		$pargs = smarty_argstr ( $_attr );
		$output .= " \$_{$pname}_data = get_data_from_cts_provider('$sink', $pargs,\$_smarty_tpl->tpl_vars);\n";
		$output .= " \$_smarty_tpl->tpl_vars['{$pname}_total'] = new Smarty_Variable;\n";
		$output .= " \$_smarty_tpl->tpl_vars['{$pname}_total']->value = \$_{$pname}_data['size'];\n";
		if ($loop) {
			$ItemVarName = '$' . $pname . '@';
			// evaluates which Smarty variables and properties have to be computed
			$usesPropFirst = strpos ( $tpl->source->content, $ItemVarName . 'first' ) !== false;
			$usesPropLast = strpos ( $tpl->source->content, $ItemVarName . 'last' ) !== false;
			$usesPropIndex = $usesPropFirst || strpos ( $tpl->source->content, $ItemVarName . 'index' ) !== false;
			$usesPropIteration = $usesPropLast || strpos ( $tpl->source->content, $ItemVarName . 'iteration' ) !== false;
			$usesPropTotal = $usesPropIteration || strpos ( $tpl->source->content, $ItemVarName . 'total' ) !== false;
			
			$output .= " \$_from = \$_{$pname}_data;\n";			
			$output .= "if (!is_array(\$_from) && !is_object(\$_from)) { settype(\$_from, 'array');}\n";
			if ($usesPropTotal) {
				$output .= " \$_smarty_tpl->tpl_vars[$item]->total= \$_smarty_tpl->_count(\$_from);\n";
			}
			if ($usesPropIteration) {
				$output .= " \$_smarty_tpl->tpl_vars[$item]->iteration=0;\n";
			}
			if ($usesPropIndex) {
				$output .= " \$_smarty_tpl->tpl_vars[$item]->index=-1;\n";
			}
			
			$output .= "foreach (\$_from as \$_smarty_tpl->tpl_vars[$item]->key => \$_smarty_tpl->tpl_vars[$item]->value){\n\$_smarty_tpl->tpl_vars[$item]->_loop = true;\n";
			
			if ($usesPropIteration) {
				$output .= " \$_smarty_tpl->tpl_vars[$item]->iteration++;\n";
			}
			if ($usesPropIndex) {
				$output .= " \$_smarty_tpl->tpl_vars[$item]->index++;\n";
			}
			if ($usesPropFirst) {
				$output .= " \$_smarty_tpl->tpl_vars[$item]->first = \$_smarty_tpl->tpl_vars[$item]->index === 0;\n";
			}
			if ($usesPropLast) {
				$output .= " \$_smarty_tpl->tpl_vars[$item]->last = \$_smarty_tpl->tpl_vars[$item]->iteration === \$_smarty_tpl->tpl_vars[$item]->total;\n";
			}
		} else {
			$output .= " \$_smarty_tpl->tpl_vars[$item]->value=\$_{$pname}_data;\n";
		}
		$output .= "?>";
		return $output;
	}
}

/**
 * Smarty Internal Plugin Compile ctselse Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Ctselse extends Smarty_Internal_CompileBase {
	
	/**
	 * Compiles code for the {ctselse} tag
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
		
		list ( $openTag, $nocache, $item, $key, $loop ) = $this->closeTag ( $compiler, array ('cts' ) );
		$this->openTag ( $compiler, 'ctselse', array ('ctselse',$nocache,$item,$key,$loop ) );
		if ($loop) {
			return "<?php }\nif (!\$_smarty_tpl->tpl_vars[$item]->_loop) { ?>";
		} else {
			return '';
		}
	}
}

/**
 * Smarty Internal Plugin Compile ctsclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Ctsclose extends Smarty_Internal_CompileBase {
	
	/**
	 * Compiles code for the {/cts} tag
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
		list ( $openTag, $compiler->nocache, $item, $key, $loop ) = $this->closeTag ( $compiler, array ('cts','ctselse' ) );
		if ($loop) {
			return "<?php } ?>";
		} else {
			return '';
		}
	}
}
