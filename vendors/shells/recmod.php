<?php

App::import('Model', 'CakeSchema', false);

/**
 * RecmodShell
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
 * @subpackage recmod.vendors.shells
 * @since      Recmod 1.0
 * @modifiedby nojimage <nojima at elasticconsultants.com>
 */
class RecmodShell extends Shell {

    function startup() {
        $this->Schema = new CakeSchema();
    }

    function main() {
        $this->help();
    }

    function help() {
        $out = array();
        $out[] = "Initialize Application commands:";
        $out[] = "\t - create <modelName>";
        $this->out(join("\n", $out));
    }

    /**
     * create command
     */
    function create() {
        if (empty($this->args)) {
            $this->error('model name is not specified.');
        }

        foreach ($this->args as $modelName) {
            $this->_create($modelName);
        }
    }

    /**
     * create log table
     *
     * @param string $modelName
     */
    function _create($modelName) {
        $modelName = Inflector::classify($modelName);
        $logModelName = $modelName . 'Log'; // TODO: read suffix option
        $logTableName = Inflector::tableize($logModelName);
        // -- load model
        $Model = ClassRegistry::init($modelName);
        $fieldPrefix = Inflector::singularize($Model->table) . '_';
        $modelSchema = $Model->schema();

        // == create log table schema
        // primaryKey
        if (!empty($modelSchema[$Model->primaryKey])) {
            $assocField = $fieldPrefix . $Model->primaryKey;
            $modelSchema[$assocField] = $modelSchema[$Model->primaryKey];
            $modelSchema[$assocField]['key'] = 'index';
            $modelSchema['indexes']['IX_' . $assocField] = array('column' => $assocField, 'unique' => 0);
            unset($modelSchema[$Model->primaryKey]);
        }

        foreach (array('created', 'modified', 'updated') as $field) {
            if (!empty($modelSchema[$field])) {
                $modelSchema[$fieldPrefix . $field] = $modelSchema[$field];
                unset($modelSchema[$field]);
            }
        }

        $modelSchema = am(array(
                    'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 20, 'key' => 'primary'),
                    'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
                        ), $modelSchema);
        $modelSchema['indexes']['PRIMARY'] = array('column' => 'id', 'unique' => 1);

        // == process
        $ds = $Model->getDataSource();
        /* @var $ds DboSource */

        $this->Schema->load(array($logTableName => $modelSchema));
        $dropStatement = $ds->dropSchema($this->Schema, $logTableName);
        $createStatement = $ds->createSchema($this->Schema, $logTableName);

        $this->out("\n" . __('The following table(s) will be dropped.', true));
        $this->out($logTableName);

        if ('y' == $this->in(__('Are you sure you want to drop the table(s)?', true), array('y', 'n'), 'n')) {
            $this->out(__('Dropping table(s).', true));
            if (!$ds->execute($dropStatement)) {
                $this->error($logTableName . ': ' . $ds->lastError());
            } else {
                $this->out(sprintf(__('%s updated.', true), $logTableName));
            }
        }

        $this->out("\n" . __('The following table(s) will be created.', true));
        $this->out($logTableName);

        if ('y' == $this->in(__('Are you sure you want to create the table(s)?', true), array('y', 'n'), 'y')) {
            $this->out(__('Creating table(s).', true));
            if (!$ds->execute($createStatement)) {
                $this->error($logTableName . ': ' . $ds->lastError());
            } else {
                $this->out(sprintf(__('%s updated.', true), $logTableName));
            }
        }
        $this->out(__('End create.', true));
    }

}