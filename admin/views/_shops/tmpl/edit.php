<?php
/**
 * Description of ShopMigrator
 *
 * @version  1.0
 * @author Daniel Eliasson Stilero Webdesign http://www.stilero.com
 * @copyright  (C) 2012-okt-07 Stilero Webdesign, Stilero AB
 * @category Components
 * @license	GPLv2
 * 
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * 
 * This file is part of edit.
 * 
 * ShopMigrator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * ShopMigrator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with ShopMigrator.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

// No direct access to this file
defined('_JEXEC') or die('fail');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
//$params = $this->state->get('params');
?> 

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'weblink.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task);
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<div class="edit<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<form action="<?php echo JRoute::_('index.php?option=com_shopmigrator&view='.JRequest::getCmd( 'view' ).'&w_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset>
		<legend><?php echo JText::_('COM_WEBLINKS_LINK'); ?></legend>

			<div class="formelm">
			<?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?>
			</div>

			<div class="formelm">
			<?php echo $this->form->getLabel('url'); ?>
			<?php echo $this->form->getInput('url'); ?>
			</div>

			<div class="formelm">
			<?php echo $this->form->getLabel('db_id'); ?>
			<?php echo $this->form->getInput('db_id'); ?>
			</div>
			<div class="formelm">
			<?php echo $this->form->getLabel('shop_system_id'); ?>
			<?php echo $this->form->getInput('shop_system_id'); ?>
			</div>
                        <div class="formelm">
                            <?php echo $this->form->getLabel('status'); ?>
                            <?php echo $this->form->getInput('status'); ?>
                        </div>
			<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('save')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('cancel')">
				<?php echo JText::_('JCANCEL') ?>
			</button>
			</div>
			<div>
			<?php echo $this->form->getLabel('description'); ?>
			<?php echo $this->form->getInput('description'); ?>
			</div>
	</fieldset>

		<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>
