<?php

App::uses('CrudAction', 'Crud.Controller/Crud/Action');
App::uses('CrudSubject', 'Crud.Controller');

class ViewCrudAction extends CrudAction {

	protected $_settings = array(
		'enabled' => true,
		'findMethod' => 'first',
		'view' => 'view'
	);

/**
 * Generic view action
 *
 * Triggers the following callbacks
 *	- Crud.init
 *	- Crud.beforeFind
 *	- Crud.recordNotFound
 *	- Crud.afterFind
 *	- Crud.beforeRender
 *
 * @param string $id
 * @return void
 */
	protected function _handle($id = null) {
		if (empty($id)) {
			$id = $this->getIdFromRequest();
		}

		$this->_validateId($id);

		// Build conditions
		$query = array();
		$query['conditions'] = array($this->_model->escapeField() => $id);

		$findMethod = $this->_getFindMethod('first');
		$subject = $this->_Crud->trigger('beforeFind', compact('id', 'query', 'findMethod'));
		$query = $subject->query;

		// Try and find the database record
		$item = $this->_model->find($subject->findMethod, $query);

		// We could not find any record match the conditions in query
		if (empty($item)) {
			$subject = $this->_Crud->trigger('recordNotFound', compact('id'));
			$this->setFlash('find.error');
			return $this->_redirect($subject, array('action' => 'index'));
		}

		// We found a record, trigger an afterFind
		$subject = $this->_Crud->trigger('afterFind', compact('id', 'item'));
		$item = $subject->item;

		// Push it to the view
		$this->_controller->set(compact('item'));

		// Trigger a beforeRender
		$this->_Crud->trigger('beforeRender', compact('id', 'item'));
	}
}
