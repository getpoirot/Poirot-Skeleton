<?php
/**
 * @see DecorateExceptionToHtml
 *
 * @var $this      \Poirot\View\ViewModel\RendererPhp
 * @var $exception \Exception
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="author" content="SmartIM">

    <title>Error::<?php echo get_class($exception); ?></title>
    <style>
        * {
            margin: 0px;
            padding: 0px;
        }

        body {
            margin: 50px;
        }

        div, p {
            font-family: Tahoma;
            font-size: 1.4em;
            color: #333333;
            margin-bottom: 10px;
            width: 60%;
            line-height: 1.2em;
        }

        h1 {
            font-family: Georgia;
            margin-bottom: 0.4em;
            font-size: 2.5em;
            font-weight: normal;
            color: #004388;
            border-bottom: 1px solid #cccccc;
            padding-bottom: 3px;
            width: 60%;
        }

        .highlight {
            padding: 9px 14px;
            margin-bottom: 14px;
            background-color: #f7f7f9;
            border: 1px solid #e1e1e8;
            border-radius: 4px;
            margin-top: 10px;
            font-size: 1.2em;
            line-height: 1.4em;
            word-wrap: break-word;
            width: 98%;
        }

        a {
            color: #004388;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            color: #004388;
        }
    </style>
</head>

<body>
    <h1>Error</h1>
    <p><?php echo $exception->getMessage(); ?></p>

    <?php if (error_reporting() != 0) { ?>

    <div class="highlight">
        <h3><?php echo get_class($exception); ?></h3>
        <dl>
            <dt><?php echo 'File'; ?>:</dt>
            <dd>
                <pre class="prettyprint linenums"><?php echo $exception->getFile() ?>:<?php echo $exception->getLine() ?></pre>
            </dd>
            <dt><?php echo 'Message' ?>:</dt>
            <dd>
                <pre class="prettyprint linenums"><?php echo $exception->getMessage() ?></pre>
            </dd>
            <dt><?php echo 'Stack trace' ?>:</dt>
            <dd>
                <pre class="prettyprint linenums"><?php echo $exception->getTraceAsString() ?></pre>
            </dd>
        </dl>

        <?php while ($exception = $exception->getPrevious()){ ?>
        <div class="highlight">
            <h3><?php echo get_class($exception); ?></h3>
            <dl>
                <dt><?php echo 'File'; ?>:</dt>
                <dd>
                    <pre class="prettyprint linenums"><?php echo $exception->getFile() ?>:<?php echo $exception->getLine() ?></pre>
                </dd>
                <dt><?php echo 'Message' ?>:</dt>
                <dd>
                    <pre class="prettyprint linenums"><?php echo $exception->getMessage() ?></pre>
                </dd>
                <dt><?php echo 'Stack trace' ?>:</dt>
                <dd>
                    <pre class="prettyprint linenums"><?php echo $exception->getTraceAsString() ?></pre>
                </dd>
            </dl>
        </div>
        <?php } ?>

    </div>

    <?php } ?>
</body>
</html>