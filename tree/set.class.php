<?php

	namespace math\tree;

	/**
	 * A set which allows unique items only, duplicate instances/objects are checked
	 * via equals(), and scalars are checked via == operator.
	 */
	class set {

		protected $items = [];

		function __constructor() {

		}

		/**
		 * Searches $this->items[] for $item, and returns 
		 * true if it's found; false if otherwise.
		 * 
		 * @param mixed $item 
		 * @return bool 
		 */
		function contains($item) {
			
			foreach($this->items as $_item) {

				if($_item->equals($item)) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Add an $item to $this->items.
		 * 
		 * @param mixed $item
		 * @return void
		 */
		function add($item) {
			if(!$this->contains($items)) {
				array_push($this->items, $item);
			}
		}

		/**
		 * Remove an $item from $this->items.
		 * 
		 * @param mixed $item
		 * @return void
		 */
		function remove($item) {
			$numItems = count($this->items);

			for($i = 0; $i < count($this->items); $i++) {
				$_item = $this->items[$i];

				// grab the last item's value, overwrite it 
				// to the found item, then pop it off the stack
				if($_item->equals($item)) {
					$this->items[$i] = $this->items[$numItems - 1];
					array_pop($this->items);
					break;
				}
			}
		}

		/**
		 * Removes all items from $this->items.
		 */
		function clear() {
			$this->items = [];
		}

		/**
		 * Returns the size of $this->items.
		 * 
		 * @return int
		 */
		function getSize() {
			return count($this->items);
		}

	}

?>