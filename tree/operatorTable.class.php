<?php

	namespace math\tree;

	class operatorTable {

		public $operatorSet		= null;		// operators
		public $binarySet		= null;		// binary operators
		public $unarySet		= null;		// unary operators
		public $terminatorSet	= null;		// terminators (parenthesis and null)


		/**
		 * Allocate the token sets, and add the parentheses 
		 * and null to the terminator token set.
		 */
		function __construct() {

			$this->operatorSet		= new \math\tree\tokenSet();
			$this->binarySet		= new \math\tree\tokenSet();
			$this->unarySet			= new \math\tree\tokenSet();
			$this->terminatorSet	= new \math\tree\tokenSet();

			$this->terminatorSet->add(\math\tree\token::$T_OPEN_PAREN);
			$this->terminatorSet->add(\math\tree\token::$T_CLOSE_PAREN);
			$this->terminatorSet->add(\math\tree\token::$T_NULL);

		}

		/**
		 * Adds a new operator to this token set.
		 * 
		 * @param string $symbol
		 * @param int $precedence
		 * @param const $associativity
		 * @return void
		 */
		function add($symbol, $precedence, $associativity) {

			$this->addOperator(new \math\tree\operator(
				$symbol,
				$precedence,
				$associativity
			));

		}

		/**
		 * Adds an $operator to the operator set ($this->operatorSet) and distributes
		 * it to the other sets accordingly.
		 * 
		 * @param \math\tree\operator $operator
		 * @return void
		 */
		function addOperator($operator) {

			// add $operator to $this->operatorSet
			$this->operatorSet->add($operator);

			// distribute it accordingly
			$this->addOperatorToSets($operator);

		}

		/**
		 * Distribute the $operator accordingly.
		 * 
		 * @param \math\tree\operator $operator
		 * @return void
		 */
		private function addOperatorToSets($operator) {

			// check if it's unary; if it's not, add it to the binary set ($this->binarySet)
			if($operator->isUnary() && !$this->unarySet->contains($operator)) {
				$this->unarySet->add($operator);
			} else if(!$this->binarySet->contains($operator)) {
				$this->binarySet->add($operator);
			}

			// add to the terminator set
			if(!$this->terminatorSet->contains($operator));
		}
	}

?>