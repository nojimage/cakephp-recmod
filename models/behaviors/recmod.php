<?php

/**
 * RecmodBehavior
 *
 * for CakePHP 1.3+
 * PHP version 5.2+
 *
 * Copyright 2011, ELASTIC Consultants Inc. (http://elasticconsultants.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version    1.0
 * @author     nojimage <nojima at elasticconsultants.com>
 * @copyright  2011, ELASTIC Consultants Inc.
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link       http://elasticconsultants.com
 * @package    recmod
 * @subpackage recmod.models.behaviors
 * @since      Recmod 1.0
 * @modifiedby nojimage <nojima at elasticconsultants.com>
 */
class RecmodBehavior extends ModelBehavior {

    protected $_savedData = array();
    /**
     *
     * @var array
     */
    public $settings = array();

    public function setup($model, $config = array()) {
        $this->settings[$model->alias] = am(array('auto' => true, 'suffix' => 'Log', 'callback' => null), $config);
    }

    /**
     *
     * @param AppModel $model
     * @param array $data
     */
    public function recordLog($model, $data = array()) {
        $ModelLog = $this->_getLogModel($model);
        if (!empty($data) && !$model->id) {
            $data = $this->_getData($model);
        }
        return $ModelLog->save($this->_filter($model, $data));
    }

    /**
     *
     * @param AppModel $model
     * @param bool $created
     */
    public function afterSave($model, $created) {

        if (!$this->settings[$model->alias]['auto']) {
            return;
        }

        $ModelLog = $this->_getLogModel($model);

        // create logging data
        $data = $model->data[$model->alias];
        if (!$created) {
            $data = $this->_getData($model);
        }
        $ModelLog->create();
        $ModelLog->save($this->_filter($model, $data));
    }

    /**
     *
     * @param AppModel $model
     * @param bool $cascade
     */
    public function beforeDelete($model, $cascade = true) {
        $this->_savedData[$model->alias] = $this->_getData($model);
        return true;
    }

    /**
     *
     * @param AppModel $model
     */
    public function afterDelete($model) {

        $ModelLog = $this->_getLogModel($model);
        if (!empty($this->_savedData[$model->alias])) {
            $ModelLog->create();
            $ModelLog->save($this->_filter($model, $this->_savedData[$model->alias]));
            unset($this->_savedData[$model->alias]);
        }
    }

    /**
     * get logging model
     *
     * @param AppModel $model
     * @return AppModel
     */
    protected function _getLogModel($model) {
        return ClassRegistry::init($model->alias . $this->settings[$model->alias]['suffix']);
    }

    /**
     *
     * @param AppModel $model 
     * @return array
     */
    protected function _getData($model) {
        $_recursive = $model->recursive;
        $model->recursive = -1;
        $data = $model->read(null, $model->id);
        $model->recursive = $_recursive;
        return $data[$model->alias];
    }

    /**
     *
     * @param AppModel $model
     * @param array $data
     * @return array
     */
    protected function _filter($model, $data) {
        $fieldPrefix = Inflector::singularize($model->table) . '_';
        if (empty($data[$model->primaryKey])) {
            $data[$fieldPrefix . $model->primaryKey] = $model->getLastInsertID();
        } else {
            $data[$fieldPrefix . $model->primaryKey] = $data[$model->primaryKey];
        }
        unset($data[$model->primaryKey]);
        foreach (array('created', 'modified', 'updated') as $field) {
            if (!empty($data[$field])) {
                $data[$fieldPrefix . $field] = $data[$field];
                unset($data[$field]);
            }
        }

        if (!empty($this->settings[$model->alias]['callback'])) {
            // callback
            if (is_string($this->settings[$model->alias]['callback']) && method_exists($model, $this->settings[$model->alias]['callback'])) {
                $data = call_user_func(array($model, $this->settings[$model->alias]['callback']), $data);
            }
        }

        return $data;
    }

}