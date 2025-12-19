<?php

use Illuminate\Validation\Validator as IlluminateValidator;

class CustomValidator extends IlluminateValidator {
	
	private $_custom_messages = array(
		"alpha_dash_spaces" 	=> "The :attribute may only contain letters, spaces, and dashes.",
		"alpha_num_spaces"		=> "The :attribute may only contain letters, numbers, and dashes.",
		"alpha_spaces"			=> "The :attribute may only contain letters and spaces.",
		"alpha_num_dash"		=> "The :attribute may only contain letters, numbers, @, and dashes.",
	);

	public function __construct($translator, $data, $rules, $messages = array(), $customAttributes = array()) {
		parent::__construct($translator, $data, $rules, $messages, $customAttributes);

		$this->_set_custom_stuff();

	}

	/**
	 * Setup any customizations etc
	 *
	 * @return  void 
	 */
	protected function _set_custom_stuff() {
		// setup our custom error messages
		$this->setCustomMessages($this->_custom_messages);
	}

	/**
	 * Allow only alphabets, spaces and dashes (hyphens and underscores)
	 *
	 * @param  string $attribute
	 * @param  mixed $value
	 * @return  bool
	 */
	protected function validateAlphaDashesSpaces($attribute, $value) {
		return (bool) preg_match("/^[A-Za-z\s-_]+$/", $value);
	}
			
	/**
	 * Allow only alphabets, numbers, and spaces
	 *
	 * @param  string $attribute
	 * @param  mixed $value
	 * @return  bool
	 */
	protected function validateAlphaNumSpaces($attribute, $value) {
		return (bool) preg_match("/^[A-Za-z0-9\s]+$/", $value);
	}

	/**
	 * Allow only alphabets and spaces
	 *@param   string $attribute
	 *@param   mixed $value
	 *@return  bool 
	 * 
	 */
	protected function validateAlphaSpaces($attribute, $value) {
		return (bool) preg_match("/^[A-Za-z\s]+$/u", $value);
	}

	/**
	 * Allow only alphabets, numbers, dashes
	 *@param   string $attribute
	 *@param   mixed $value
	 *@return  bool 
	 * 
	 */
	protected function validateAlphaNumDash($attribute, $value) {
		return (bool) preg_match("/^[A-Za-z0-9\-\_\@\.]+$/u", $value);
	}
	
}	//end of class

?>