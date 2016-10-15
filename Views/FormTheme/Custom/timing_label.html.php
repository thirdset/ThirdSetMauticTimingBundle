<?php
/**
 * @package     ThirdSetMauticTimingBundle
 * @copyright   2016 Third Set Productions. All rights reserved.
 * @author      Third Set Productions
 * @link        http://www.thirdset.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * This template was copied from CoreBundle\Views\FormTheme\Custom\form_label.html.php
 * It modifies the label for the timing fields.
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
?>

<?php if (false !== $label): ?>
<?php if ($required) {
    $label_attr['class'] = trim((isset($label_attr['class']) ? $label_attr['class'] : '').' required');
} ?>
<?php if (!$compound) {
    $label_attr['for'] = $id;
} ?>
<?php if (!$label) {
    $label = $view['form']->humanize($name);
} ?>
<?php $tooltip = (!empty($form->vars['attr']['tooltip'])) ? $form->vars['attr']['tooltip'] : false; ?>
<h3 <?php foreach ($label_attr as $k => $v) {
    printf('%s="%s" ', $view->escape($k), $view->escape($v));
} ?><?php if ($tooltip): ?>data-toggle="tooltip" data-container="body" data-placement="top" title="<?php echo $view['translator']->trans($tooltip); ?>"<?php endif; ?>><?php echo $view->escape($view['translator']->trans($label, [], $translation_domain)) ?><?php if ($tooltip): ?> <i class="fa fa-question-circle"></i><?php endif; ?></h3>
<h6 class="text-muted" style="margin-bottom: 1em;">Use the settings in this section to control when this event can occur.</h6>
<?php endif ?>

