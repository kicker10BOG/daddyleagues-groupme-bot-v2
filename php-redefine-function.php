<?php

// --------------------------------------------

// from https://gist.github.com/Bubujka/915401
class Memo{
	static $fns = array();
}

function defun($name, $fn){
	Memo::$fns[$name] = $fn;
	if(!function_exists($name))
		eval('function '.$name.'(){'.
		     '$args = func_get_args();'.
		     'return call_user_func_array(Memo::$fns[\''.$name.'\'], $args);}');
}

function wrap($name, $fn, $new_name = false){
	$old_fn = Memo::$fns[$name];
	if(!$new_name)
		$new_name = $name;
	defun($new_name, wrapper($old_fn, $fn));
}

function wrapper($old_fn, $new_fn){
	return function() use($old_fn, $new_fn){
		$args = func_get_args();
		return $new_fn(function() use($args, $old_fn){return call_user_func_array($old_fn, $args);});
	};
}

// --------------------------------------------

?>