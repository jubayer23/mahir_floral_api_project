<?php
class Validator
{
	protected $_enable_xss = FALSE;
	
	public function valid_email($str)
	{
		if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches))
		{
			$domain = defined('INTL_IDNA_VARIANT_UTS46')
				? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46)
				: idn_to_ascii($matches[2]);

			if ($domain !== FALSE)
			{
				$str = $matches[1].'@'.$domain;
			}
		}

		return (bool) filter_var($str, FILTER_VALIDATE_EMAIL);
	}
	public function min_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return ($val <= mb_strlen($str));
	}
	public function max_length($str, $val)
	{
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return ($val >= mb_strlen($str));
	}
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}
	public function in_list($value, $list)
	{
		return in_array($value,$list,TRUE);
	}
	public function regex_match($str, $regex)
	{
		return (bool) preg_match($regex, $str);
	}
	function validate_float($var)
	{
		return filter_var($var,FILTER_VALIDATE_FLOAT);
	}
	function sanitize_string($var)
	{
		return filter_var($var,FILTER_SANITIZE_STRING);
	}
	
}
