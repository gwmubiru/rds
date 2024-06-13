<?php
    define('IN_CB', true);

    // include('include/header.php');

    ?>
<?php
if (!defined('IN_CB')) { die('You are not allowed to access to this page.'); }

if (version_compare(phpversion(), '5.0.0', '>=') !== true) {
    exit('Sorry, but you have to run this script with PHP5... You currently have the version <b>' . phpversion() . '</b>.');
}

if (!function_exists('imagecreate')) {
    exit('Sorry, make sure you have the GD extension installed before running this script.');
}

// include_once('function.php');
if (!defined('IN_CB')) { die('You are not allowed to access to this page.'); }


$imageKeys = array();
function registerImageKey($key, $value) {
    global $imageKeys;
    $imageKeys[$key] = $value;
}

function getImageKeys() {
    global $imageKeys;
    return $imageKeys;
}

function getElementHtml($tag, $attributes, $content = false) {
    $code = '<' . $tag;
    foreach ($attributes as $attribute => $value) {
        $code .= ' ' . $attribute . '="' . htmlentities(stripslashes($value), ENT_COMPAT) . '"';
    }

    if ($content === false || $content === null) {
        $code .= ' />';
    } else {
        $code .= '>' . $content . '</' . $tag . '>';
    }

    return $code;
}

function getInputTextHtml($name, $currentValue, $attributes = array()) {
    $defaultAttributes = array(
        'id' => $name,
        'name' => $name
    );

    $finalAttributes = array_merge($defaultAttributes, $attributes);
    if ($currentValue !== null) {
        $finalAttributes['value'] = $currentValue;
    }

    return getElementHtml('input', $finalAttributes, false);
}

function getOptionGroup($options, $currentValue) {
    $content = '';
    foreach ($options as $optionKey => $optionValue) {
        if (is_array($optionValue)) {
            $content .= '<optgroup label="' . $optionKey . '">' . getOptionGroup($optionValue, $currentValue) . '</optgroup>';
        } else {
            $optionAttributes = array();
            if ($currentValue == $optionKey) {
                $optionAttributes['selected'] = 'selected';
            }
            $content .= getOptionHtml($optionKey, $optionValue, $optionAttributes);
        }
    }

    return $content;
}

function getOptionHtml($value, $content, $attributes = array()) {
    $defaultAttributes = array(
        'value' => $value
    );

    $finalAttributes = array_merge($defaultAttributes, $attributes);

    return getElementHtml('option', $finalAttributes, $content);
}

function getSelectHtml($name, $currentValue, $options, $attributes = array()) {
    $defaultAttributes = array(
        'size' => 1,
        'id' => $name,
        'name' => $name
    );

    $finalAttributes = array_merge($defaultAttributes, $attributes);
    $content = getOptionGroup($options, $currentValue);

    return getElementHtml('select', $finalAttributes, $content);
}

function getCheckboxHtml($name, $currentValue, $attributes = array()) {
    $defaultAttributes = array(
        'type' => 'checkbox',
        'id' => $name,
        'name' => $name,
        'value' => isset($attributes['value']) ? $attributes['value'] : 'On'
    );

    $finalAttributes = array_merge($defaultAttributes, $attributes);
    if ($currentValue == $finalAttributes['value']) {
        $finalAttributes['checked'] = 'checked';
    }

    return getElementHtml('input', $finalAttributes, false);
}

function getButton($value, $output = null) {
    $escaped = false;
    $finalValue = $value[0] === '&' ? $value : htmlentities($value);
    if ($output === null) {
        $output = $value;
    } else {
        $escaped = true;
    }

    $code = '<input type="button" value="' . $finalValue . '" data-output="' . $output . '"' . ($escaped ? ' data-escaped="true"' : '') . ' />';
    return $code;
}

/**
 * Returns the fonts available for drawing.
 *
 * @return string[]
 */
function listfonts($folder) {
    $array = array();
    if (($handle = opendir($folder)) !== false) {
        while (($file = readdir($handle)) !== false) {
            if(substr($file, -4, 4) === '.ttf') {
                $array[$file] = $file;
            }
        }
    }
    closedir($handle);

    array_unshift($array, 'No Label');

    return $array;
}

/**
 * Returns the barcodes present for drawing.
 *
 * @return string[]
 */
function listbarcodes() {
    include_once('barcode.php');
    $using_laravel = true;

    $availableBarcodes = array();
    foreach ($supportedBarcodes as $file => $title) {
        if ($using_laravel || file_exists($file)) {
            $availableBarcodes[$file] = $title;
        }
    }

    return $availableBarcodes;
}

function findValueFromKey($haystack, $needle) {
    foreach ($haystack as $key => $value) {
        if (strcasecmp($key, $needle) === 0) {
            return $value;
        }
    }

    return null;
}

function convertText($text) {
    $text = stripslashes($text);
    if (function_exists('mb_convert_encoding')) {
        $text = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }

    return $text;
}

// FileName & Extension
$system_temp_array = explode('/', $_SERVER['PHP_SELF']);
$filename = $system_temp_array[count($system_temp_array) - 1];
$system_temp_array2 = explode('.', $filename);
$availableBarcodes = listbarcodes();
$barcodeName = findValueFromKey($availableBarcodes, $filename);
$code = $system_temp_array2[0];

// // Check if the code is valid
// if (file_exists('config' . DIRECTORY_SEPARATOR . $code . '.php')) {
//     include_once('config' . DIRECTORY_SEPARATOR . $code . '.php');
// }
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $barcodeName; ?> - Barcode Generator</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link type="text/css" rel="stylesheet" href="/bc/style.css" />
        <link rel="shortcut icon" href="favicon.ico" />
        <script src="/bc/jquery-1.7.2.min.js"></script>
        <script src="/bc/barcode.js"></script>
    </head>
    <body class="<?php echo $code; ?>">

<?php
$default_value = array();
$default_value['filetype'] = 'PNG';
$default_value['dpi'] = 72;
$default_value['scale'] = isset($defaultScale) ? $defaultScale : 1;
$default_value['rotation'] = 0;
$default_value['font_family'] = 'Arial.ttf';
$default_value['font_size'] = 8;
$default_value['text'] = '123456789';
$default_value['a1'] = '';
$default_value['a2'] = '';
$default_value['a3'] = '';

$filetype = isset($_POST['filetype']) ? $_POST['filetype'] : $default_value['filetype'];
$dpi = isset($_POST['dpi']) ? $_POST['dpi'] : $default_value['dpi'];
$scale = intval(isset($_POST['scale']) ? $_POST['scale'] : $default_value['scale']);
$rotation = intval(isset($_POST['rotation']) ? $_POST['rotation'] : $default_value['rotation']);
$font_family = isset($_POST['font_family']) ? $_POST['font_family'] : $default_value['font_family'];
$font_size = intval(isset($_POST['font_size']) ? $_POST['font_size'] : $default_value['font_size']);
$text = isset($_POST['text']) ? $_POST['text'] : $default_value['text'];

registerImageKey('filetype', $filetype);
registerImageKey('dpi', $dpi);
registerImageKey('scale', $scale);
registerImageKey('rotation', $rotation);
registerImageKey('font_family', $font_family);
registerImageKey('font_size', $font_size);
registerImageKey('text', stripslashes($text));

// Text in form is different than text sent to the image
$text = convertText($text);
?>

<div class="header">
    <header>
        <img class="logo" src="/bc/logo.png" alt="Barcode Generator" />
        <nav>
            <label for="type">Symbology</label>
            <?php 
            echo getSelectHtml('type', $filename, $availableBarcodes); 
            ?>
            <a class="info explanation" href="#"><img src="/bc/info.gif" alt="Explanation" /></a>
        </nav>
    </header>
</div>

{!! Form::open( array('url'=>'/bc2', 'method' => 'post') ) !!}

    <h1>Barcode Generator</h1>
    <h2><?php echo $barcodeName; ?></h2>
    <div class="configurations">
        <section class="configurations">
            <h3>Configurations</h3>
            <table>
                <colgroup>
                    <col class="col1" />
                    <col class="col2" />
                </colgroup>
                <tbody>
                    <tr>
                        <td><label for="filetype">File type</label></td>
                        <td><?php echo getSelectHtml('filetype', $filetype, array('PNG' => 'PNG - Portable Network Graphics', 'JPEG' => 'JPEG - Joint Photographic Experts Group', 'GIF' => 'GIF - Graphics Interchange Format')); ?></td>
                    </tr>
                    <tr>
                        <td><label for="dpi">DPI</label></td>
                        <td><?php echo getInputTextHtml('dpi', $dpi, array('type' => 'number', 'min' => 72, 'max' => 300, 'required' => 'required')); ?> <span id="dpiUnavailable">DPI is available only for PNG and JPEG.</span></td>
                    </tr>
<?php
// if (isset($baseClassFile) && file_exists('include' . DIRECTORY_SEPARATOR . $baseClassFile)) {
//     include_once('include' . DIRECTORY_SEPARATOR . $baseClassFile);
// }
?>
                    <tr>
                        <td><label for="scale">Scale</label></td>
                        <td><?php 
                        echo getInputTextHtml('scale', $scale, array('type' => 'number', 'min' => 1, 'max' => 4, 'required' => 'required')); 
                        ?>
                    </td>
                    </tr>
                    <tr>
                        <td><label for="rotation">Rotation</label></td>
                        <td>
                            <?php 
                                echo getSelectHtml('rotation', $rotation, array(0 => 'No rotation', 90 => '90&deg; clockwise', 180 => '180&deg; clockwise', 270 => '270&deg; clockwise')); 
                                ?>
                                </td>
                    </tr>
                    <tr>
                        <td><label for="font_family">Font</label></td>
                        <td><?php 
                        echo getSelectHtml('font_family', $font_family, listfonts('font')); ?> <?php echo getInputTextHtml('font_size', $font_size, array('type' => 'number', 'min' => 1, 'max' => 30)); 

                        ?></td>
                    </tr>
                    <tr>
                        <td><label for="text">Data</label></td>
                        <td>
                            <div class="generate" style="float: left"><?php echo getInputTextHtml('text', $text, array('type' => 'text', 'required' => 'required')); ?> <input type="submit" value="Generate" /></div>
                            <div class="possiblechars" style="float: right; position: relative;"><a href="#" class="info characters"><img src="/bc/info.gif" alt="Help" /></a></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
<?php

    $default_value['start'] = '';
    $start = isset($_POST['start']) ? $_POST['start'] : $default_value['start'];
    registerImageKey('start', $start);
    registerImageKey('code', 'BCGcode128');

    $vals = array();
    for($i = 0; $i <= 127; $i++) {
        $vals[] = '%' . sprintf('%02X', $i);
    }
    $characters = array(
        'NUL', 'SOH', 'STX', 'ETX', 'EOT', 'ENQ', 'ACK', 'BEL', 'BS', 'TAB', 'LF', 'VT', 'FF', 'CR', 'SO', 'SI', 'DLE', 'DC1', 'DC2', 'DC3', 'DC4', 'NAK', 'SYN', 'ETB', 'CAN', 'EM', 'SUB', 'ESC', 'FS', 'GS', 'RS', 'US',
        '&nbsp;', '!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ':', ';', '<', '=', '>', '?',
        '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '[', '\\', ']', '^', '_',
        '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '{', '|', '}', '~', 'DEL'
    );
?>

<ul id="specificOptions">
    <li class="option">
        <div class="title">
            <label for="start">Starts with</label>
        </div>
        <div class="value">
            <?php echo getSelectHtml('start', $start, array('NULL' => 'Auto', 'A' => 'Code 128-A', 'B' => 'Code 128-B', 'C' => 'Code 128-C')); ?>
        </div>
    </li>
</ul>

<div id="validCharacters">
    <h3>Valid Characters</h3>
    <?php $c = count($characters); for ($i = 0; $i < $c; $i++) { echo getButton($characters[$i], $vals[$i]); } ?>
</div>

<div id="explanation">
    <h3>Explanation</h3>
    <ul>
        <li>Code 128 is a high-density alphanumeric symbology.</li>
        <li>Used extensively worldwide.</li>
        <li>Code 128 is designed to encode 128 full ASCII characters.</li>
        <li>The symbology includes a checksum digit.</li>
        <li>Code 128A handles capital letters<br />Code 128B handles capital letters and lowercase<br />Code 128C handles group of 2 numbers</li>
        <li>Your browser may not be able to write the special characters (NUL, SOH, etc.) but you can write them with the code.</li>
    </ul>
</div>

<?php
if (!defined('IN_CB')) { die('You are not allowed to access to this page.'); }
?>

            <div class="output">
                <section class="output">
                    <h3>Output</h3>
                    <?php
                        $finalRequest = '';
                        foreach (getImageKeys() as $key => $value) {
                            $finalRequest .= '&' . $key . '=' . urlencode($value);
                        }
                        if (strlen($finalRequest) > 0) {
                            $finalRequest[0] = '?';
                        }
                        echo "FINAL: $finalRequest";
                    ?>
                    <div id="imageOutput">
                        <?php if (1 || $imageKeys['text'] !== '') { 


                            ?>
                            <img src="/img<?php echo $finalRequest; ?>" alt="Barcode Image" />
                            xxxx
                            <?php }
                        else { ?>Fill the form to generate a barcode.<?php } ?>
                    </div>

                </section>
            </div>
        
        {!! Form::close() !!}

        <div class="footer">
            <footer>
            All Rights Reserved &copy; <?php date_default_timezone_set('UTC'); echo date('Y'); ?> <a href="http://www.barcodephp.com" target="_blank">Barcode Generator</a>
            <br />
            </footer>
        </div>
    </body>
</html>