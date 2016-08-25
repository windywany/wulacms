<?php
/**
 * Smarty Internal Plugin Compile Ctsp from Foreach
 *
 * Compiles the {ctsp}{/ctsp} tags
 *
 * @package Smarty
 * @subpackage Compiler
 * @author Leo Ning
 */

/**
 * Smarty Internal Plugin Compile Ctsp Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Ctsp extends Smarty_Internal_CompileBase {
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array('var');
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array('_any');

    /**
     * Compiles code for the {ctsp} tag
     *
     * @param array  $args      array with attributes from parser
     * @param object $compiler  compiler object
     * @param array  $parameter array with compilation parameter
     * @return string compiled code
     */
    public function compile($args, $compiler, $parameter) {
        $_attr = $this->getAttributes($compiler, $args);
        $name = isset($_attr ['for']) ? $_attr ['for'] : '__this';

        $pitem = "'" . trim($_attr ['var'], '\'"') . "'";
        $render = "'default'";
        if (isset ($_attr ['render']) && !empty ($_attr ['render'])) {
            $render = $_attr ['render'];
        }
        if ($name == '__this') {
            $pname = "_smarty_tpl->tpl_vars['__this_data']->value";
        } else {
            $pname = trim($name, "'\"");
            $pname = "_{$pname}_data";
        }        
        
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        unset ($_attr ['for'], $_attr ['render'], $_attr['item']);
        $argstr = smarty_argstr($_attr);
        $this->openTag($compiler, 'ctsp', array('ctsp', $compiler->nocache, $pitem));
        $output = "<?php\n";
        $output .= " \$_smarty_tpl->tpl_vars[$pitem] = new Smarty_Variable();\n";
        $output .= "\$_from = \${$pname}->getRenderData($render, $argstr);\n";
        $output .= "if (is_string(\$_from)){ echo \$_from; } else {\n";
        $output .= "foreach (\$_from as \$_smarty_tpl->tpl_vars[$pitem]->key => \$_smarty_tpl->tpl_vars[$pitem]->value){?>\n";
        return $output;
    }
}

/**
 * Smarty Internal Plugin Compile ctspclose Class
 *
 * @package Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Ctspclose extends Smarty_Internal_CompileBase {
    /**
     * Compiles code for the {/ctsp} tag
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
        list ($openTag, $compiler->nocache, $item) = $this->closeTag($compiler, array('ctsp'));
        return "<?php }} ?>";
    }
}

?>