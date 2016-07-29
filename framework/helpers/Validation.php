<?php

namespace Framework\Helpers;

class Validation
{
    public $validation_errors = array();

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    public function validate($data, $rules)
    {

        $valid = true;

        // extracts callback from $rule

        foreach ($rules as $fieldname => $rule) {
            // Extract rules as callbacks

            $callbacks = explode('|', $rule);

            // Call the validation callback

            foreach ($callbacks as $callback) {
                $value = isset($data[$fieldname]) ? $data[$fieldname] : null;

                $params = $this->parseParam($callback);

                $callback = $this->parseCallback($callback);

                if ($this->$callback($value, $fieldname, $params) == false) {
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateParseCallback($callback)
    {
        $colon = strpos($callback, ':');
        $length = strlen($callback);

        if ($colon == false) {
            $rule = $callback;
        } else {
            $rule = substr($callback , 0, $length - ($length - $colon));
        }

        return $rule;
    }

    // extracts $params array from $rule

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateParseParam($callback)
    {
        $param_list = null;
        $colon = strpos($callback, ':');
        $params = array();

        if ($colon == false)
        {
            $param = null;
        } else {
            $param_list = substr($callback , $colon + 1);
        }

        if ($param_list != null)
        {
            $params = explode(':', $param_list);
        }

        return $params;
    }

    // checks that a value is numeric

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateNumeric($value, $fieldname)
    {

        $pattern = '/^[0-9.,]*$/';

        if (preg_match($pattern, $value))
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "Please provide a valid number $fieldname";
        }

        return $valid;
    }

    // checks that a value is currency

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateCurrency($value, $fieldname)
    {
        $pattern = '/^[0-9.,$]*$/';

        if (preg_match($pattern, $value))
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "Please provide a valid number $fieldname";
        }

        return $valid;
    }

    // checks that a value is an integer

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateInteger($value, $fieldname)
    {
        $valid = filter_var($value, FILTER_VALIDATE_INT);

        if ($valid == false)
        {
            $this->validation_errors[] = "Please provide a whole number for $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks for an integer that is less than a max value

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateMax($value, $fieldname, $params)
    {
        $options = array (
            'options' => array(
                'max_range' => $params[0]
          )
      );

        $valid = filter_var($value, FILTER_VALIDATE_INT, $options);

        if ($valid == false)
        {
            $this->validation_errors[] = "Please provide a whole number less than $params[0] for $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks for an integer that is greater than a min value

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateMin($value, $fieldname, $params)
    {
        $options = array (
            'options' => array(
                'min_range' => $params[0]
          )
      );

        $valid = filter_var($value, FILTER_VALIDATE_INT, $options);

        if ($valid == false)
        {
            $this->validation_errors[] = "Please provide a whole number greater than $params[0] for $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks for an integer that falls within a specified range

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateRange($value, $fieldname, $params)
    {
        $options = array (
            'options' => array(
                'min_range' => $params[0],
                'max_range' => $params[1]
          )
      );

        $valid = filter_var($value, FILTER_VALIDATE_INT, $options);

        if ($valid == false)
        {
            $this->validation_errors[] = "Please provide a whole number between $params[0] and $params[1] for $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // most form values are at least 3 characters long
    // automatically rejects any value that is less than 3 characters in length

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateShort($value, $fieldname)
    {
        $value = trim($value);

        if (strlen($value) > 3)
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "$fieldname should be more than two characters";
        }

        return $valid;
    }

    // checks that a value is alpha characters only

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateAlpha($value, $fieldname)
    {
        $pattern = '/^[a-z]*$/i';

        if (preg_match($pattern, $value))
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "Please provide a valid $fieldname";
        }

        return $valid;
    }

    // checks a value for a minimum string length

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateMinLength($value, $fieldname, $params)
    {
        $value = trim($value);

        if (strlen($value) > $params[0])
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "$fieldname should be more than $params[0] characters";
        }

        return $valid;
    }

    // checks a value for a maximum string length

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateMaxLength($value, $fieldname, $params)
    {
        $value = trim($value);

        if (strlen($value) < $params[0])
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "$fieldname should be less than $params[0] characters";
        }

        return $valid;
    }

    // checks a value for an exact string length

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateLength($value, $fieldname, $params)
    {
        $value = trim($value);

        if (strlen($value) == $params[0])
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "$fieldname should be exactly $params[0] characters";
        }

        return $valid;
    }

    // checks a value for a string length range

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateBetweenLength($value, $fieldname, $params)
    {
        $value = trim($value);
        $str_len = strlen($value);
        $min = $params[0];
        $max = $params[1];

        if ($str_len >= $min and $str_len <= $max)
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "$fieldname should be between $min and $max characters";
        }

        return $valid;
    }

    // accepts alpha characters, ' ', and '.'
    // Does not ensure that value is actually a first name and last name
    // Only looks for acceptable chararacters that would appear in a person's full name
    // Examples: John Q. Public, John, John Doe

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateFullName($value, $fieldname)
    {

        $pattern = '/^[a-z.,\s]*$/i';

        if (preg_match($pattern, $value))
        {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "Please provide a valid $fieldname";
        }

        return $valid;
    }

    // validates that a ten digit phone number with an optional extension is provided
    // only validates US phone numbers for now

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validatePhone($value, $fieldname)
    {

        function cleanPhone($value)
        {

            // remove whitespace
            $value = trim($value);

            // look for extension followed by 'x'
            // split phone number and extension into two seperate strings
            if (strpos($value, 'x') != false)
            {
                $complete_phone = preg_split('/[x]/i', $value);

                $value = $complete_phone[0];
            }

            // strip spaces and everything that is not a number
            $value = preg_replace('/[^0-9]*/', '', $value);

            // strip leading 1
            $value = preg_replace('/\b[1]/', '', $value);

            return $value;
        }

        // no bad area codes or toll free / pay per call
        // BAD_AREA_CODES = open('bad-area-codes.txt', 'r').read().split('\n');

        // make sure phone number is exactly 10 digits

        if (strlen($value) < 10)
        {
            $valid = false;
            $this->validation_errors[] = "Too short! Please provide a valid phone number for $fieldname";
        } else {
            $phone = cleanPhone($value);

            if (strlen($phone) != 10)
            {
                $valid = false;
                $this->validation_errors[] = "Please provide a valid phone number for $fieldname";
            } else {
                $valid = true;
            }
        }

        return $valid;
    }

    // checks for a valid email

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateEmail($value, $fieldname)
    {
        $valid = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($valid == false)
        {
            $this->validation_errors[] = "Please provide a valid email address for $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks for a valid url

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateUrl($value, $fieldname)
    {
        $valid = filter_var($value, FILTER_VALIDATE_URL);

        if ($valid == false)
        {
            $this->validation_errors[] = "Please provide a valid url for $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks a string to see if it contains any urls

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateNoUrl($value, $fieldname)
    {
        $pattern = '/(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?/i'; // url

        if (preg_match($pattern, $value))
        {
            $valid = false;
            $this->validation_errors[] = "Invalid $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks a string to see if it contains any html tags

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateNoTags($value, $fieldname)
    {
        $pattern = '/[<>]/'; // tags

        if (preg_match($pattern, $value))
        {
            $valid = false;
            $this->validation_errors[] = "$fieldname cannot contain html";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks to see that any required fields are not null

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateRequired($value, $fieldname)
    {
        $valid = !empty($value);

        if ($valid == false)
        {
            $this->validation_errors[] = "The $fieldname is required";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // checks for header attack attempts

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateAttacks($value, $fieldname)
    {
        $pattern = '/Content-Type:|Bcc:|Cc:/i';

        if (preg_match($pattern, $value))
        {
            $valid = false;
            $this->validation_errors[] = "Invalid $fieldname";
        } else {
            $valid = true;
        }

        return $valid;
    }

    // honeypot

    /**
     *
     *
     * @since  1.0.0
     * @access public
     * @param
     * @return void
     */

    private function validateHoneypot($value, $fieldname)
    {
        if ((!$value)) {
            $valid = true;
        } else {
            $valid = false;
            $this->validation_errors[] = "There was an error processing your message";
        }

        return $valid;
    }
}