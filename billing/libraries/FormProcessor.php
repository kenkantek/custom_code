<?php
    abstract class FormProcessor
    {
        protected $_errors = array();
        protected $_vals = array();
      

        public function __construct()
        {

        }

      
	    abstract function process($request);

		

        public function addError($key, $val)
        {
            if (array_key_exists($key, $this->_errors)) {
                if (!is_array($this->_errors[$key]))
                    $this->_errors[$key] = array($this->_errors[$key]);

                $this->_errors[$key][] = $val;
            }
            else
                $this->_errors[$key] = $val;
        }

        public function getError($key)
        {
            if ($this->hasError($key))
                return $this->_errors[$key];

            return null;
        }

        public function getErrors()
        {
            return $this->_errors;
        }

        public function hasError($key = null)
        {
            if (strlen($key) == 0)
                return count($this->_errors) > 0;

            return array_key_exists($key, $this->_errors);
        }

        public function __set($name, $value)
        {
            $this->_vals[$name] = $value;
        }

        public function __get($name)
        {
            return array_key_exists($name, $this->_vals) ? $this->_vals[$name] : null;
        }
        
        public function anti_sql($str)
        {
        	if(get_magic_quotes_gpc()){
        		return $str;
        	}
        	return mysql_escape_string($str);
        }
    }
?>