<?php
/**
 * Created by PhpStorm.
 * User: wenzi
 * Date: 11/26/13
 * Time: 3:51 PM
 */

function generate_barcode($text, $file_name)
{
    // Including all required classes
    require_once('barcode/class/BCGFontFile.php');
    require_once('barcode/class/BCGColor.php');
    require_once('barcode/class/BCGDrawing.php');

    // Including the barcode technology
    require_once('barcode/class/BCGcode39.barcode.php');

    // Loading Font
    $CURRENT_TEMPLATE_URL = dirname(Yii::app()->basePath)  . Yii::app()->params['THEME_BASE_URL'];
    $font = new BCGFontFile($CURRENT_TEMPLATE_URL.'/fonts/helvetica_neue_thin.ttf', 18);

    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);

    $drawException = null;
    try {
        $code = new BCGcode39();
        $code->setScale(2); // Resolution
        $code->setThickness(40); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
        $code->clearLabels();
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
    1 - Filename (empty : display on screen)
    2 - Background color */
    $drawing = new BCGDrawing($file_name, $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}