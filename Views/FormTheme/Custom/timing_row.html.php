<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * This template was copied from CoreBundle\Views\FormTheme\Custom\form_row.html.php
 * It modifies the row for the timing fields and adds an <hr/> tag above it.
 * 
 * The folder of theme files is registered in the ThirdSetMauticTimingBundle
 * class.
 * 
 * You can see what other theme files are searched for by editing
 * Symfony\Bundle\FrameworkBundle\Templating\Loader\FileSystemLoader->load()
 *   Add the following line at the top of the function:
 *       echo $template->getPath();
 *   Then load the form in a browser. Note that you may need to find the output 
 * by using the browser's Developer Tools.
 */

$hasErrors     = count($form->vars['errors']);
$feedbackClass = (!empty($hasErrors)) ? ' has-error' : '';
?>
<hr/>
<div class="row">
    <div class="form-group col-xs-12 <?php echo $feedbackClass; ?>">
        <?php echo $view['form']->label($form, $label) ?>
        <?php echo $view['form']->widget($form) ?>
        <?php echo $view['form']->errors($form) ?>
    </div>
</div>