<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$user  = JFactory::getUser();
$input = JFactory::getApplication()->input;
?>
<div class="row-fluid">
	<!-- Begin Sidebar -->
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
		<hr/>
		<div class="j-toggle-sidebar-header">
		<h3 style="padding-left: 10px;"><?php echo JText::_('COM_MEDIA_FOLDERS');?> </h3>
		</div>
		<div id="treeview" class="sidebar">
			<div id="media-tree_tree" class="sidebar-nav">
				<?php echo $this->loadTemplate('folders'); ?>
			</div>
			<script>
				jQuery('#j-toggle-sidebar-button').click(function(){
					MediaManager.setTreeviewState();	
				});
			</script>
		</div>
	</div>
	<!-- End Sidebar -->

	<!-- Begin Content -->
	<div id="j-main-container" class="span10">
		<?php echo $this->loadTemplate('navigation'); ?>
		<?php if (($user->authorise('core.create', 'com_media')) and $this->require_ftp) : ?>
			<div class="well well-small">
				<fieldset title="<?php echo JText::_('COM_MEDIA_DESCFTPTITLE'); ?>">
					<legend><?php echo JText::_('COM_MEDIA_DESCFTPTITLE'); ?></legend>
					<p><?php echo JText::_('COM_MEDIA_DESCFTP'); ?></p>

					<form action="<?php echo JRoute::_('index.php?option=com_media&controller=media.ftpvalidate.media'); ?>" name="ftpForm" id="ftpForm" method="post" class="form-inline">
						<label for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?>
							<input type="text" id="username" name="username" class="input-medium" value="" />
						</label>
						<label for="password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>
							<input type="password" id="password" name="password" class="input-medium" value="" />
						</label>
						<button type="submit" class="btn btn-primary"><?php echo JText::_('JSUBMIT'); ?></button>
					</form>
				</fieldset>
			</div>			
		<?php endif; ?>

		<form action="index.php?option=com_media" name="adminForm" id="mediamanager-form" method="post" enctype="multipart/form-data" >
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="cb1" id="cb1" value="0" />
			<input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $this->state->get('folder'); ?>" />
		</form>

		<?php if ($user->authorise('core.create', 'com_media')):?>
		
	<!-- File Upload Form Modal -->
	<form action="<?php echo JRoute::_('index.php?option=com_media&controller=media.upload.media&format=html'); ?>" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data" >
	<div id="uploadModal" class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">&times;</button>
			<h3>
				<?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?>
			</h3>
		</div>
		<div class="modal-body">

			<?php 
			echo JHtml::_('bootstrap.startAccordion', 'collapseTypes', array('active' => 'collapse_dragndrop'));

			echo JHtml::_('bootstrap.addSlide', 'collapseTypes', JText::_('COM_MEDIA_UPLOADER_DRAGNDROP'), 'collapse_dragndrop');
			?>

				<!-- Ajax based Drag&Drop Uploader -->
				<div id="dragandrophandler" class="hero-unit"><h2>Drag & Drop Files Here</h2></div>

						<table class="table table-striped">
							<tbody id="upload-container">	 						
							</tbody>					
						</table>

				<input type="hidden" id="form-token" value="<?php echo JSession::getFormToken();?>" />
			<?php echo JHtml::_('bootstrap.endSlide'); ?>

			<?php echo JHtml::_('bootstrap.addSlide', 'collapseTypes', JText::_('COM_MEDIA_UPLOADER_REGULAR'), 'collapse_regular'); ?>

				<!-- Regular Uploader -->
				<div id="" class="form-horizontal">
					<fieldset id="upload-noflash" class="actions">
						<label for="upload-file" class="control-label"><?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?></label>
						<input type="file" id="upload-file" name="Filedata[]" multiple /> 
						<p class="help-block"><?php echo $this->config->get('upload_maxsize') == '0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></p>
					</fieldset>
						<input class="update-folder" type="hidden" name="folder" id="folder" value="<?php echo $this->state->get('folder'); ?>" />
						<?php JFactory::getSession()->set('com_media.return_url', 'index.php?option=com_media'); ?>

						<button class="btn btn-primary" id="upload-submit">
							<span class="icon-upload icon-white"></span> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>
						</button>
				</div>

			<?php echo JHtml::_('bootstrap.endSlide'); ?>
			<?php echo JHtml::_('bootstrap.endAccordion'); ?>

		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_MEDIA_CLOSE'); ?>
			</a>
			
		</div>
	</div>
		<?php echo JHtml::_('form.token'); ?>
	</form>


	<!-- New Folder Form Modal -->

	<form action="<?php echo JRoute::_('index.php?option=com_media&controller=media.create.medialist'); ?>" method="post">
	<div id="newfolderModal" class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">&times;</button>
			<h3>
				<?php echo JText::_('COM_MEDIA_CREATE_FOLDER'); ?>
			</h3>
		</div>
		<div class="modal-body">
			<div id="" class="form-horizontal">
						<input class="input-xlarge" type="text" id="folderpath" readonly="readonly" />
						<input class="input-medium" type="text" id="foldername" name="foldername" required />
						<input class="update-folder" type="hidden" name="folderbase" id="folderbase" value="<?php echo $this->state->get('folder'); ?>" />
			</div>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_MEDIA_CLOSE'); ?>
			</a>
			<button class="btn btn-primary" type="submit">
				<span class="icon-folder-open"></span> <?php echo JText::_('COM_MEDIA_CREATE_FOLDER'); ?>
			</button>
		</div>
	</div>
		<?php echo JHtml::_('form.token'); ?>
	</form>

	<!-- Copy Media Modal -->

	<div id="copyMediaModal" class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">&times;</button>
			<h3>
				<?php echo JText::_('COM_MEDIA_COPY_MEDIA'); ?>
			</h3>
		</div>
		<div class="modal-body">
			<div id="" class="form-horizontal">
				<div class="control-group">
					<label class="control-label"><?php echo JText::_('COM_MEDIA_COPY_TO_DIRECTORY') ?></label>
					<div class="controls">
						<div class="input-append" id="copyTarget">			  	
							<?php echo $this->folderList; ?>
						</div>	
					</div>
				</div>				
			</div>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_MEDIA_CLOSE'); ?>
			</a>
			<button class="btn btn-primary" onclick="MediaManager.submitWithTargetPath('media.copy.media')">
				<span class="icon-copy"></span> <?php echo JText::_('COM_MEDIA_COPY_MEDIA'); ?>
			</button>
		</div>
	</div>

	<!-- Move Media Modal -->

	<div id="moveMediaModal" class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">&times;</button>
			<h3>
				<?php echo JText::_('COM_MEDIA_MOVE_MEDIA'); ?>
			</h3>
		</div>
		<div class="modal-body">
			<div id="" class="form-horizontal">
				<div class="control-group">
					<label class="control-label"><?php echo JText::_('COM_MEDIA_MOVE_TO_DIRECTORY') ?></label>
					<div class="controls">
						<div class="input-append" id="moveTarget">			  	
						<?php echo $this->folderList; ?>
						</div>	
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_MEDIA_CLOSE'); ?>
			</a>
			<button class="btn btn-primary" onclick="MediaManager.submitWithTargetPath('media.move.media')">
				<span class="icon-copy"></span> <?php echo JText::_('COM_MEDIA_MOVE_MEDIA'); ?>
			</button>
		</div>
	</div>	
		
		<?php endif;?>

		<?php if ($user->authorise('core.delete', 'com_media')):?>

	<!-- Delete Media Modal -->

	<div id="deleteMediaModal" class="modal hide fade">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"
				aria-hidden="true">&times;</button>
			<h3>
				<?php echo JText::_('COM_MEDIA_DELETE_MEDIA'); ?>
			</h3>
		</div>
		<div class="modal-body">
			<p id="" class="lead">
						<?php echo JText::_('COM_MEDIA_DELETE_MEDIA_MSG'); ?>
			</p>
		</div>
		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal"><?php echo JText::_('COM_MEDIA_CLOSE'); ?>
			</a>
			<button class="btn btn-danger" type="submit" onclick="MediaManager.submit('media.delete.media')">
				<span class="icon-remove"></span> <?php echo JText::_('COM_MEDIA_DELETE_MEDIA'); ?>
			</button>
		</div>
	</div>

		<?php endif;?>


		<form action="index.php?option=com_media&amp;task=folder.create&amp;tmpl=<?php echo $input->getCmd('tmpl', 'index');?>" name="folderForm" id="folderForm" method="post">
			<div id="folderview">
				<div class="view">
					<iframe class="thumbnail" src="index.php?option=com_media&amp;controller=media.display.medialist&amp;view=medialist&amp;tmpl=component&amp;folder=<?php echo $this->state->get('folder');?>" id="folderframe" name="folderframe" width="100%" height="500px" marginwidth="0" marginheight="0" scrolling="auto"></iframe>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
	<!-- End Content -->
</div>
