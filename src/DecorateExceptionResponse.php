<?php
namespace Poirot\Skeleton;

use Poirot\Std\Environment\EnvServerDefault;
use Poirot\View\ViewModel\RendererPhp;


class DecorateExceptionResponse
{
    /** @var \Exception */
    protected $e;

    /**
     * Constructor.
     * @param \Exception $e
     */
    function __construct($e)
    {
        if (!($e instanceof \Throwable || $e instanceof \Exception) )
            throw new \InvalidArgumentException(sprintf(
                'Invalid Argument (%s) must be an \Exception or \Throwable.'
                , get_class($e)
            ));

        $this->e = $e;
    }

    function __toString()
    {
        $e = $this->e;

        if (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') {
            $exception_code = $e->getCode();

            $exRef = new \ReflectionClass($e);
            $result = array(
                'status' => 'ERROR',
                'error'  => array(
                    'state'   => $exRef->getShortName(),
                    'code'    => $exception_code,
                    'message' => $e->getMessage(),
                ),
            );

            $isAllowDisplayExceptions = new EnvServerDefault();
            $isAllowDisplayExceptions = $isAllowDisplayExceptions->getErrorReporting();

            if ($isAllowDisplayExceptions) {
                do {
                    $result = array_merge_recursive($result, array(
                        'error' => array(
                            '_debug_' => array(
                                'exception' => array(
                                    array(
                                        'message' => $e->getMessage(),
                                        'class'   => get_class($e),
                                        'file'    => $e->getFile(),
                                        'line'    => $e->getLine(),
                                    ),
                                ),
                            ),
                        ),
                    ));
                } while ($e = $e->getPrevious());
            }

            \Poirot\Http\Response\httpResponseCode(500);
            header('Content-Type: application/json');
            echo json_encode($result);
            die;
        }

        if (ob_get_level())
            ## clean output buffer, display just error page
            ob_end_clean();

        try {
            return $this->toHtml();
        } catch(\Exception $ve) {
            ## throw exception if can't render template
            return sprintf(
                'Error While Rendering Exception Into HTML!!! (%s)'
                , $e->getMessage()
            );
        }
    }

    /**
     * Print Exception Object Error Page
     *
     * @return string
     * @throws \Throwable
     */
    function toHtml()
    {
        $e = $this->e;

        try {
            $renderer = new RendererPhp();
            return $renderer->capture(
                __DIR__ . '/../.error.page.php'
                , array('exception' => $e)
            );
        } catch(\Exception $ve) {
            ## throw exception if can't render template
            throw $e;
        }
    }
}