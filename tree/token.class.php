<?php

	namespace math\tree;

	class token {

		// static tokens (defined below this class... for now)
		static $T_OPEN_PAREN		= null;
		static $T_CLOSE_PAREN		= null;
		static $T_NULL				= null;

		// tokens
		const T_NONE				= null;		// none/null
		const T_UNKNOWN				= 0;		// unknown
		const T_ID					= 1;		// identifier
		const T_OPERAND				= 2;		// operator
		const T_FUNCTION			= 3;		// function
		const T_STRING				= 4;		// string
		const T_NUMBER				= 5;		// number
		const T_OPEN_PARENTHESIS	= 6;		// open parenthesis
		const T_CLOSE_PARENTHESIS	= 7;		// closed parenthesis
		const T_DECIMAL				= 8;		// decimal
		// const T_HEXADECIMAL			= 9;	// hexadecimal
		const T_NULL				= 10;		// null

		// regex for decimals
		const REGEX_DECIMAL			= "/^[-+]?(?:\\.{1}[\\d]+|[\\d]+\\.?[\\d]*)(?:[Ee]{1}[-+]?[\\d]+)?/";

		// data
		public $rawValue			= "";				// raw value passed in constructor
		public $value				= "";				// parsed $rawValue
		public $type				= self::T_UNKNOWN;	// token constant

		/**
		 * Constructor
		 * 
		 * @param mixed $value
		 * @param int $type
		 */
		function __construct($rawValue = "", $type = self::T_UNKNOWN) {
			
			$this->value	= $value;
			$this->rawValue	= $rawValue;
			$this->type		= $type;
		}

		/**
		 * Parses $this->rawValue (if $this->type is a 
		 * string or an identifier) and sets $this->value.
		 * 
		 * @return void
		 */
		private function parseRawValue() {

			// ensure we don't have any quotes in strings or identifier types
			if($this->type == self::T_ID || $this->type == self::T_STRING) {
				$this->value = $this->unquote($this->rawValue);
				return;
			}

			$this->value = $this->rawValue;
		}

		/**
		 * Remove quotation characters from $string.
		 * 
		 * @param string $string
		 * @return string
		 */
		static function unquote($string) {

			$chars = "`'\"";

			// empty input?
			if($string == "") {
				return $string;
			}

			$strArr = str_split($string);

			$isEscaped	= false;
			$style		= $strArr[0];

			// check if there aren't any quotations
			if(strpos($chars, $style) === false) {
				return $string;
			}

			$ret = "";

			for($i = 1; $i < count($strArr); $i++) {

				$char = $strArr[$i];

				if($char == "\\" && !$isEscaped) {
					$isEscaped = true;
				} else if($char == $style && !$isEscaped) {

					// look ahead for the ending quotation
					if(count($strArr) > $i + 1 && $strArr[$i + 1] == $style) {
						// fixme: is this necessary? why not just break?
						$isEscaped = true;
					}

					break;

				} else {
					$isEscaped = false;
					$ret .= $char;
				}
			}

			return $ret;
		}

		/**
		 * Compares two tokens against one another.
		 * 
		 * @param $token \math\tree\token
		 * @return bool
		 */
		function equals($token) {

			if($token == null) {
				return false;
			}

			if($this->type != $token->type) {
				return false;
			}

			if($this->value != $token->value) {
				return false;
			}

			return true;
		}

	}

	// add parentheses and null tokens
	// todo: find a better way to init the static tokens
	token::$T_OPEN_PAREN	= new token("(", token::T_OPEN_PARENTHESIS);
	token::$T_CLOSE_PAREN	= new token(")", token::T_CLOSE_PARENTHESIS);
	token::$T_NULL			= new token("(", token::T_NULL);

?>