<?php
/**
 * HtException class file.
 */

/**
 * HtException represents an exception that is caused by some Biz-related operations.
 *
 * @author wenzi@hitour.cc
 * @since 1.0
 */
class HtException extends CException
{
	/**
	 * @var mixed the error info provided by a  exception. This is the same as returned
	 */
	public $errorInfo;

	/**
	 * Constructor.
	 * @param string $message  error message
	 * @param integer $code  error code
	 * @param mixed $errorInfo  error info
	 */
	public function __construct($message,$code=0,$errorInfo=null)
	{
		$this->errorInfo=$errorInfo;
		parent::__construct($message,$code);
	}
}