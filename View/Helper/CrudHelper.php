<?php
App::uses('CrudAppHelper', 'Crud.View/Helper');

class CrudHelper extends CrudAppHelper {

/**
 * List of helpers used by this helper
 *
 * @var array
 */
	public $helpers = array(
		'Form',
		'Html',
		'Time',
	);

/**
 * Returns a formatted output for a given field
 *
 * @param string $field name of the field
 * @param mixed $value the value that the field should have within related data
 * @param array $data an array of data related to this field
 * @param array $schema a Model schema
 * @param array $associations an array of associations to be used
 * @var string formatted value
 */
	public function format($field, $value, $data, $schema = array(), $associations = array()) {
		$output = $this->relation($field, $data, $associations);
		if ($output) {
			return $output['output'];
		}

		$type = Hash::get($schema, "{$field}.type");
		if ($type == 'boolean') {
			return !!$value ? 'Yes' : 'No';
		} elseif (in_array($type, array('datetime', 'date', 'timestamp'))) {
			return $this->Time->timeAgoInWords($value);
		} elseif ($type == 'time') {
			return $this->Time->nice($value);
		}
		return h(String::truncate($value, 200));
	}

/**
 * Returns a formatted relation output for a given field
 *
 * @param string $field name of the field
 * @param array $data an array of data related to this field
 * @param array $associations an array of associations to be used
 * @var mixed array of data to output, false if no match found
 */
	public function relation($field, $data, $associations = array()) {
		if (!empty($associations['belongsTo'])) {
			foreach ($associations['belongsTo'] as $_alias => $_details) {
				if ($field === $_details['foreignKey']) {
					return array(
						'alias' => $_alias,
						'output' => $this->Html->link($data[$_alias][$_details['displayField']], array(
							'controller' => $_details['controller'],
							'action' => 'view',
							$data[$_alias][$_details['primaryKey']]
						)),
					);
				}
			}
		}

		return false;
	}

/**
 * Returns a hidden input for the redirect_url if it exists
 * in the request querystring, view variables, form data
 *
 * @var array
 */
	public function redirectUrl() {
		$redirect_url = $this->_View->request->query('redirect_url');
		if (!empty($this->_View->viewVars['redirect_url'])) {
			$redirect_url = $this->_View->viewVars['redirect_url'];
		} else {
			$redirect_url = $this->Form->value('redirect_url');
		}

		if (!empty($redirect_url)) {
			return $this->Form->hidden('redirect_url', array(
				'name' => 'redirect_url',
				'value' => $redirect_url,
				'id' => null,
				'secure' => FormHelper::SECURE_SKIP
			));
		}

		return null;
	}

}