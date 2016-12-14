<?php
namespace Module\Foundation\Actions\Helper;


use Poirot\Storage\Gateway\DataStorageSession;

class FlashMessageAction
{
    // Message types and shortcuts
    const INFO    = 'info';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR   = 'error';

    // Default message type
    const MESSAGE_TYPE_DEFAULT = self::INFO;

    const NAMESPACE_DEFAULT    = 'default';
    const SESSION_REALM        = 'poirot.flash_messenger';


    /** @var string */
    protected $nameSpaceCurr;
    protected $_session;


    /**
     * FlashMessageAction constructor.
     * @param string $messageNamespace
     */
    function __construct($messageNamespace = self::NAMESPACE_DEFAULT)
    {
        $this->nameSpaceCurr = (string) $messageNamespace;
    }

    /**
     * @param string $messageNamespace
     *
     * @return $this
     */
    function __invoke($messageNamespace = self::NAMESPACE_DEFAULT)
    {
        return new self($messageNamespace);
    }


    /**
     * Add a flash message to the session data
     *
     * @param  string $message The message text
     * @param  string $type    The message type
     * @param  array  $meta    Meta data for message such as sticky
     *
     * @return $this
     */
    function add($message, $type=self::MESSAGE_TYPE_DEFAULT, array $meta=null)
    {
        // normalize type
        $type = strtolower($type);

        $messageQueue = $this->_getMessages();
        if (!isset($messageQueue[$type]))
            $messageQueue[$type] = array();

        $messageQueue[$type][] = array(
            'message' => (string) $message,
            'type'    => $type,
            'meta'    => $meta
        );

        $this->_session()->set($this->nameSpaceCurr, $messageQueue);
        return $this;
    }

    /**
     * See if there are any error messages?
     *
     * @return boolean
     *
     */
    function hasErrors()
    {
        return $this->hasMessages(self::ERROR);
    }

    /**
     * See if there are any queued message?
     *
     * @param string $type The Message Type
     *
     * @return array|false
     *
     */
    function hasMessages($type = null)
    {
        $messages = $this->_session()->get($this->nameSpaceCurr);
        if ( $type !== null && is_array($messages) ) {
            // normalize type
            $type = strtolower($type);
            $messages = ( isset($messages[$type]) ) ? $messages[$type] : false;
        }

        return ($messages) ? $messages : false;
    }

    /**
     * Fetch the flash messages and clear queue
     *
     * @param null|string $type
     *
     * @return array
     */
    function fetchMessages($type = null)
    {
        $return = array();
        $quMess = $this->_getMessages($type);
        foreach ($quMess as $qType => $qTypeMessages) {
            // Retrieve the messages, then remove them from session
            if ($type !== null && $qType != $type)
                continue;

            $return[$qType][] = $qTypeMessages;
            unset($quMess[$qType]);
        }

        $this->_session()->set($this->nameSpaceCurr, $quMess);
        return $return;
    }

    /**
     * Add an info message
     *
     * @param  string $message The message text
     * @param  array  $meta    Meta data for message such as sticky
     *
     * @return $this
     *
     */
    function info($message, array $meta=null)
    {
        return $this->add($message, self::INFO, $meta);
    }

    /**
     * Add a success message
     *
     * @param  string $message The message text
     * @param  array  $meta    Meta data for message such as sticky
     *
     * @return $this
     *
     */
    function success($message, array $meta=null)
    {
        return $this->add($message, self::SUCCESS, $meta);
    }

    /**
     * Add a warning message
     *
     * @param  string  $message      The message text
     * @param  array  $meta    Meta data for message such as sticky
     *
     * @return $this
     *
     */
    function warning($message, array $meta=null)
    {
        return $this->add($message, self::WARNING, $meta);
    }

    /**
     * Add an error message
     *
     * @param  string $message The message text
     * @param  array  $meta     Meta data for message such as sticky
     *
     * @return $this
     *
     */
    function error($message, array $meta=null)
    {
        return $this->add($message, self::ERROR, $meta);
    }


    // ..

    /**
     * Get Message Queue
     *
     * [
     *   'info' => [
     *       [
     *          'message' => (string)
     *          'meta'    => []
     *       ],
     *       ...
     *   ],
     *   'warning' => ...
     *
     * @param string|null $type The Message Type
     *
     * @return array
     */
    protected function _getMessages($type = null)
    {
        return ($message = $this->hasMessages($type)) ? $message : array();
    }

    /**
     * Get Session Storage
     * @return DataStorageSession
     */
    function _session()
    {
        if(!$this->_session) {
            $session = new DataStorageSession;
            // Store in session by realm defined with this authentication domain
            $session->setRealm(self::SESSION_REALM);
            $this->_session = $session;
        }

        return $this->_session;
    }
}
