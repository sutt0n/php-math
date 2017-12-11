<?php

	namespace math\tree;

	class operator extends token {

		// associativity
		const UNARY	= 0;
		const LEFT	= 1;
		const RIGHT	= 2;

		public $precedence		= 0;
		public $associativity	= 0;

		/**
		 * Constructor
		 * 
		 * @param string $symbol
		 * @param int $precedence
		 * @param const $associativity
		 */
		function __construct($symbol, $precedence, $associativity) {

			$this->value			= $symbol;
			$this->type				= Token::T_OPERAND;
			$this->precedence		= $precedence;
			$this->associativity	= $associativity;
		}

		/**
		 * Determines if this operator equals another $operator.
		 * 
		 * @param \math\tree\operator $operator
		 * @return bool True only if the operators are equal.
		 */
		function equals($operator) {

			// is the $operator null?
			if($operator == null) {
				return false;
			}

			// check type
			if($this->type != $operator->type) {
				return false;
			}

			// check value
			if($this->value != $operator->value) {
				return false;
			}

			// check precedence
			if($this->precedence != $operator->precedence) {
				return false;
			}

			// check associativity
			if($this->associativity != $operator->associativity) {
				return false;
			}

			return true;

		}

		/**
		 * Determines if the operator is unary or not.
		 * 
		 * @return bool True if the operator is unary, false otherwise.
		 */
		function isUnary() {
			return self::UNARY == $this->associativity;
		}		

	}

?>