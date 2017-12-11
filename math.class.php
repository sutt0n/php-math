<?php

	namespace math;

	/**
	 * Algebraic Parse Tree
	 * 
	 * @class math
	 * @author Joseph Sutton <ebwebdev@gmail.com>
	 * @description A simple math class for solving algebraic equations via parsing expressions by recursive descent.
	 */
	class math {

		/** @var \math\tree\Tree $tree */
		var $tree = null;

		// variable-related properties
		var $variablePrefix = "$";
		var $vars			= [];

		// operator precedence
		const OP_PREC_POW	= 2;
		const OP_PREC_DIV	= 1;
		const OP_PREC_MUL	= 1;
		const OP_PREC_ADD	= 0;
		const OP_PREC_SUB	= 0;

		// operators
		const OP_DIV		= "/";
		const OP_MUL		= "*";
		const OP_ADD		= "+";
		const OP_SUB		= "-";

		var $expr			= "";
		var $stack			= [];
		var $treeBuilt		= false;

		var $debug			= false;

		public function __construct($expr, $vars = [], $variablePrefix = "$", $debugMode = false) {

			// todo: assert

			// set variables
			$this->variablePrefix	= $variablePrefix;
			$this->vars				= $vars;
			$this->expr				= $expr;
			$this->debug 			= $debugMode;

			// parse variables
			$resParsedVars = $this->parseVariables();

			$result->addChild($resParsedVars);

			if(!$this->variablesParsed()) {
				return false;
			} else {
				$this->buildTree();
			}

			if($this->debug) {
				// todo: add debug factory
			}
		}

		private function variablesParsed() {

			if(preg_match("/[a-zA-Z]+/", $this->expr)) {
				return false;
			}

			return true;

		}

		/**
		 * Evaluate the equation, provided all variables have been provided.
		 *
		 * @param bool $divZeroForceZero TRUE forces division by zero to be evaluated as zero. FALSE will not
		 * suppress this error.
		 *
		 * @return bool|int Returns FALSE if all variables haven't been replaced in the equation.
		 * Otherwise, return the solution (an integer).
		 */
		public function evaluate($divZeroForceZero = false) {

			if(!$this->treeBuilt) {
				return false;
			}

			$tokens = $this->tree->getRecursiveTokens();
			$stack = [];

			foreach($tokens as $token) {

				$value = $token->value;

				switch($token->type) {
					case Token::T_DECIMAL:
						array_push($stack, $value);
						break;

					case (Token::T_OPERAND): {
						$left = array_pop($stack);
						$right = array_pop($stack);

						switch($value) {
							case self::OP_ADD:
								array_push($stack, ($left + $right));
								break;

							case self::OP_SUB:
								array_push($stack, ($left - $right));
								break;

							case self::OP_MUL:
								array_push($stack, ($left * $right));
								break;

							case self::OP_DIV:
								if($divZeroForceZero && $right == 0) {
									array_push($stack, 0);
								} else {
									array_push($stack, ($left / $right));
								}
								break;
						}
						break;
					}
				}

			}

			return array_pop($stack);

		}

		/**
		 * Construct the parse tree.
		 *
		 * @return void
		 */
		private function buildTree() {

			$parser = new Parser();
			$this->tree = $parser->parse($this->expr);

			$this->treeBuilt = true;

		}

		/**
		 * Parse variables in the expression.
		 *
		 * @param $expression
		 * @param $vars
		 * @return string New evaluated expression.
		 */
		private function parseVariables() {

			$expression = $this->expr;
			$vars		= $this->vars;

			// apply variable prefix
			if($this->variablePrefix != "") {

				// "Prepending variable prefix '". $this->variablePrefix ."' to given variables.";

				$_vars = [];

				// prepend variablePrefix to identifiers
				foreach($vars as $identifier => $var) {

					 // "Prepended '". $this->variablePrefix ."' to variable '". $identifier ."' successfully."

					$_vars[$this->variablePrefix . $identifier] = $var;
				}

				// "Variables successfully prepended with prefix, '". $this->variablePrefix ."'."

				$vars = $_vars;

				// clear
				unset($var);
			}

			// ensure there's whitespace between operators/variables
			$expression = str_replace("(", " ( ", $expression);
			$expression = str_replace(")", " ) ", $expression);
			$expression = str_replace("+", " + ", $expression);
			$expression = str_replace("-", " - ", $expression);
			$expression = str_replace("/", " / ", $expression);
			$expression = str_replace("*", " * ", $expression);

			// remove too much whitespace
			$expression = preg_replace("!\\s+!", " ", $expression);

			 // "Replacing variables in expression with given variable values."

			$this->expr = str_replace(array_keys($vars), $vars, $expression);

			return $result;

		}

	}

?>