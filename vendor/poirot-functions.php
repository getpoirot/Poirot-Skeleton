<?php
namespace Poirot
{
    use Poirot\View\Interpreter\IsoRenderer;

    /**
     * Print Exception Object Error Page
     *
     * @param \Exception $e
     *
     * @throws \Exception cant render exception
     */
    function print_exception(\Exception $e) {
        if (ob_get_level())
            ## clean output buffer, display just error page
            ob_end_clean();
        try {
            echo (new IsoRenderer())->capture(
                APP_DIR_THEME_DEFAULT.'/error/general.php'
                , ['exception' => $e]
            );
        } catch(\Exception $ve) {
            ## throw exception if can't render template
            throw $e;
        }
    }
}
