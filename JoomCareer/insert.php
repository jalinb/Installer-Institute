<?php
/**
 * @version		$Id: insert.php 2427 2014-03-21 15:48:16Z admin $
 * @package		Joomla.Site
 * @subpackage	com_joomcareer
 * 
 * @author		WERI-Design GbR www.weri-design.de
 * @copyright	Copyright (C) 2013 WERI-Design GbR. All rights reserved.
 * @license		GNU/GPL, see license.php
 */

// No direct access to this file
defined('_JEXEC') or die;

// import CONTROLLER object class
jimport( 'joomla.application.component.controllerform' );

class JoomcareerControllerInsert extends JControllerForm
{
	protected $view_item = 'insert';
	protected $view_list = 'jobs';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JControllerLegacy
	 * @since   12.2
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	
		// Guess the option as com_NameOfController
		if (empty($this->option))
		{
			$this->option = 'com_' . strtolower($this->getName());
		}
	
		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix))
		{
			$this->text_prefix = strtoupper($this->option);
		}
	
		// Guess the context as the suffix, eg: OptionControllerContent.
		if (empty($this->context))
		{
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r))
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->context = strtolower($r[2]);
		}
	
		// Guess the item view as the context.
		if (empty($this->view_item))
		{
			$this->view_item = $this->context;
		}
	
		// Guess the list view as the plural of the item view.
		if (empty($this->view_list))
		{
			// @TODO Probably worth moving to an inflector class based on
			// http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
	
			// Simple pluralisation based on public domain snippet by Paul Osman
			// For more complex types, just manually set the variable in your class.
			$plural = array(
					array('/(x|ch|ss|sh)$/i', "$1es"),
					array('/([^aeiouy]|qu)y$/i', "$1ies"),
					array('/([^aeiouy]|qu)ies$/i', "$1y"),
					array('/(bu)s$/i', "$1ses"),
					array('/s$/i', "s"),
					array('/$/', "s"));
	
			// Check for matches using regular expressions
			foreach ($plural as $pattern)
			{
				if (preg_match($pattern[0], $this->view_item))
				{
					$this->view_list = preg_replace($pattern[0], $pattern[1], $this->view_item);
					break;
				}
			}
		}
	
		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');
	}

	/**
	 * Method to add a new record.
	 *
	 * @return	boolean	True if the job can be added, false if not.
	 * @since	1.6
	 */
	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}
	
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$allow		= null;
	
		$allow	= $user->authorise('core.create', 'com_joomcareer');
	
		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else {
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();
		$userId = $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_joomcareer'))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_joomcareer'))
		{
			// Now test the owner is the user.
			$ownerId 	= (int) isset($data['user_id']) ? $data['user_id'] : 0;
			$firmId 	= (int) isset($data['firm_id']) ? $data['firm_id'] : 0;
			
			$firm = $this->getModel()->getFirm($userId);
			
			if ((empty($ownerId) || empty($firmId)) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}

				$ownerId = $record->user_id;
				$firmId = $record->firm_id;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
			elseif ($firmId == $firm) {
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}
	
	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   12.2
	 */
	public function edit($key = null, $urlVar = 'id')
	{
		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();
		$cid   = $app->input->post->get('cid', array(), 'array');
		$context = "$this->option.edit.$this->context";
	
		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}
	
		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}
	
		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : $app->input->getInt($urlVar));
		$checkin = property_exists($table, 'checked_out');
			
		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
	
			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(), false
					)
			);
	
			return false;
		}
			
		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
	
			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
			);
	
			return false;
		}
		else
		{
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
			$app->setUserState($context . '.data', null);
	
			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
			);
	
			return true;
		}
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 *
	 * @return	Boolean	True if access level checks pass, false otherwise.
	 * @since	1.6
	 */
	public function cancel($key = 'id')
	{
		$app   = JFactory::getApplication();
		
		$app->setUserState($context . '.data', null);
		$app->setUserState('job.data', null);
		
		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}
	
	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function delete()
	{
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->getInt('id', NULL);

		if ($cid)
		{			
			// Get the model.
			$model = $this->getModel();		

			// Remove the items.
			if ($model->delete($cid))
			{
				JError::raiseNotice(100, JText::_('JOB_DELETED'));
				$this->setRedirect($this->getReturnPage());
			}
			else
			{
				JError::raiseWarning(100, JText::_('ERROR_DELETING_JOB'));
				$this->setRedirect($this->getReturnPage());
			}
		}
	}

	public function getModel($name = 'Insert', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
	
	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	protected function getReturnPage()
	{
		$app = JFactory::getApplication();
		
		$return = $app->input->get('return', null, 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$input = JFactory::getApplication()->input;
		
		// Need to override the parent method completely.
		$layout		= $input->get('layout', 'edit', 'string');

		$append		= '';

		if ($layout) {
			$append .= '&layout='.$layout;
		}

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= $input->get('Itemid', '', 'int');
		$return	= $this->getReturnPage();

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		if ($return) {
			$append .= '&return='.base64_encode($return);
		}

		return $append;
	}
	
	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = 'id')
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$data  = $app->input->post->get('jform', array(), 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();
		
		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}
		
		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}
		
		$recordId = $app->input->getInt($urlVar);
		
		// Populate the row id from the session.
		$data[$key] = $recordId;
		
		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Check-in the original row.
			if ($checkin && $model->checkin($data[$key]) === false)
			{
				// Check-in failed. Go back to the item and display a notice.
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
				$this->setMessage($this->getError(), 'error');
		
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
		
				return false;
			}
		
			// Reset the ID and then treat the request as for Apply.
			$data[$key] = 0;
			$task = 'apply';
		}
		
		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
		
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
		
			return false;
		}
		
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);
		
		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');
		
			return false;
		}
		
		// Test whether the data is valid.
		$validData = $model->validate($form, $data);
		
		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();
		
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
		
			// Save the data in the session.
			$app->setUserState($context . '.data', $data);
			$app->setUserState('job.data', $data);
			
			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);
		
			return false;
		}
		
		if (!isset($validData['tags']))
		{
			$validData['tags'] = null;
		}
		
		// Attempt to save the data.
		if (!$model->save($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);
			$app->setUserState('job.data', $data);
		
			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
		
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);
		
			return false;
		}
		
		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);
			$app->setUserState('job.data', $data);
		
			// Check-in failed, so go back to the record and display a notice.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
		
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);
		
			return false;
		}
		
		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
				? $this->text_prefix
				: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);
		
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
			// Set the record data in the session.
			$recordId = $model->getState($this->context . '.id');
			$this->holdEditId($context, $recordId);
			$app->setUserState($context . '.data', null);
			$app->setUserState('job.data', null);
			$model->checkout($recordId);
		
			// Redirect back to the edit screen.
			$this->setRedirect('index.php/employer-dashboard');
			break;
		
			case 'save2new':
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
				$app->setUserState('job.data', null);
		
				// Redirect back to the edit screen.
				$this->setRedirect($this->getReturnPage());
				break;
		
			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
				$app->setUserState('job.data', null);

				// Redirect to the list screen.
				$this->setRedirect('index.php/employer-dashboard');
				break;
			}
		
		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);
		
		return true;		
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Joomcareer', $prefix = 'JoomcareerTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}
