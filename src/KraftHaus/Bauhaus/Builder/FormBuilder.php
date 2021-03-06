<?php

namespace KraftHaus\Bauhaus\Builder;

/**
 * This file is part of the KraftHaus Bauhaus package.
 *
 * (c) KraftHaus <hello@krafthaus.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Closure;
use Illuminate\Support\Facades\Validator;
use KraftHaus\Bauhaus\Result\FormResult;
use KraftHaus\Bauhaus\Field\BaseField;

/**
 * Class ListBuilder
 * @package KraftHaus\Bauhaus\Builder
 */
class FormBuilder extends BaseBuilder
{

	const CONTEXT_CREATE = 'create';
	const CONTEXT_EDIT = 'edit';

	/**
	 * Holds the form result.
	 * @var array
	 */
	protected $result = [];

	/**
	 * Holds the record identifier(id).
	 * @var int
	 */
	protected $identifier;

	/**
	 * Set the form identifier.
	 *
	 * @param  int $identifier
	 *
	 * @access public
	 * @return FormBuilder
	 */
	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;
		return $this;
	}

	/**
	 * Get the form identifier.
	 *
	 * @access public
	 * @return int
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * Set the form context.
	 *
	 * @param  FormBuilder $context
	 *
	 * @access public
	 * @return FormBuilder
	 */
	public function setContext($context)
	{
		$this->context = $context;
		return $this;
	}

	/**
	 * Get the form context.
	 *
	 * @access public
	 * @return mixed
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Build the list data.
	 *
	 * @access public
	 * @return mixed|void
	 */
	public function build()
	{
		$formMapper = $this->getMapper();
		$model = $this->getModel();

		/**
		 * Empty form
		 */
		$result = new FormResult();
		if ($this->getIdentifier() === null) {
			foreach ($formMapper->getFields() as $field) {
				$clone = clone $field;
				$name  = $clone->getName();

				$clone->setContext(BaseField::CONTEXT_FORM);
				$result->addField($name, $clone);
			}

			$this->setResult($result);
			return;
		}

		$items = $model::with([]);

		$items->where('id', $this->getIdentifier());
		$item = $items->first();

		$result = new FormResult;
		$result->setIdentifier($item->id);

		foreach ($formMapper->getFields() as $field) {
			$clone = clone $field;
			$name  = $clone->getName();
			$value = $item->{$name};

			if ($clone->hasBefore()) {
				$before = $clone->getBefore();

				if ($before instanceof Closure) {
					$value = $before($value);
				} else {
					$value = $before;
				}
			}

			$clone
				->setContext(BaseField::CONTEXT_FORM)
				->setValue($value);

			$result->addField($name, $clone);
		}

		$this->setResult($result);
	}

	/**
	 * Sets the form result.
	 *
	 * @param  array $result
	 *
	 * @access public
	 * @return FormBuilder
	 */
	public function setResult(FormResult $result)
	{
		$this->result = $result;
		return $this;
	}

	/**
	 * Returns the form result.
	 *
	 * @access public
	 * @return array
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * Create a new model from input.
	 * 
	 * @param  Input $input
	 *
	 * @access public
	 * @return FormBuilder
	 */
	public function create($input)
	{
		$model = $this->getModel();
		$this->setInput($input);

		// Field pre update
		foreach ($this->getMapper()->getFields() as $field) {
			$field->preUpdate();

			if ($field->hasSaving()) {
				$saving = $field->getSaving();
				$this->setInputVariable($field->getName(), $saving($input[$field->getName()]));
			}
		}

		// Validate
		if (property_exists($model, 'rules')) {
			$validator = Validator::make($this->getInput(), $model::$rules);
			if ($validator->fails()) {
				return $validator;
			}
		}

		// Create hook
		if (method_exists($this->getMapper()->getAdmin(), 'create')) {
			$this->getMapper()->getAdmin()->create($this->getInput());
		} else {
			$model::create($this->getInput());
		}

		// Field post update
		foreach ($this->getMapper()->getFields() as $field) {
			$field->postUpdate($this->getInput());
		}

		return $this;
	}

	/**
	 * Update a model from input.
	 * 
	 * @param  Input $input
	 *
	 * @access public
	 * @return FormBuilder
	 */
	public function update($input)
	{
		$model = $this->getModel();
		$this->setInput($input);

		// Field pre update
		foreach ($this->getMapper()->getFields() as $field) {
			$field->preUpdate();

			if ($field->hasSaving()) {
				$saving = $field->getSaving();
				$this->setInputVariable($field->getName(), $saving($input[$field->getName()]));
			}
		}

		// Validate
		if (property_exists($model, 'rules')) {
			$validator = Validator::make($this->getInput(), $model::$rules);
			if ($validator->fails()) {
				return $validator;
			}
		}

		// Update hook
		if (method_exists($this->getMapper()->getAdmin(), 'update')) {
			$this->getMapper()->getAdmin()->update($this->getInput());
		} else {
			$model::find($this->getIdentifier())
				->update($this->getInput());
		}

		// Field post update
		foreach ($this->getMapper()->getFields() as $field) {
			$field->postUpdate($this->getInput());
		}

		return $this;
	}

	/**
	 * Destroy a specific item.
	 *
	 * @access public
	 * @return FormBuilder
	 */
	public function destroy()
	{
		$model = $this->getModel();
		$model::find($this->getIdentifier())->delete();

		return $this;
	}

}