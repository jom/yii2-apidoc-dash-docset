<?php
/**
 * @copyright Copyright (c) 2013 Jacob Morrison
 * @license BSD-3-Clause
 */

namespace yii\apidoc\templates\docset;

use Yii;
use SQLite3;

/**
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Renderer extends \yii\apidoc\templates\offline\Renderer
{
	public $db;
	public $plistFile = 'Info.plist';

	public function render($context, $controller)
	{
		if (substr($this->targetDir, -7) !== '.docset') {
			$this->targetDir .= '.docset';
		}

		$originalTargetDir = $this->targetDir;
		$sqliteDb = $originalTargetDir . DIRECTORY_SEPARATOR . 'Contents' . DIRECTORY_SEPARATOR .'Resources' . DIRECTORY_SEPARATOR. 'docSet.dsidx';
		$plistPath = $originalTargetDir . DIRECTORY_SEPARATOR . 'Contents' .  DIRECTORY_SEPARATOR . 'Info.plist';
		$this->targetDir = $originalTargetDir . DIRECTORY_SEPARATOR . 'Contents' . DIRECTORY_SEPARATOR .'Resources' . DIRECTORY_SEPARATOR . 'Documents';

		if (!is_dir($this->targetDir)) {
			mkdir($this->targetDir, 0755, true);
		}

		if (is_file($sqliteDb)) {
			unlink($sqliteDb);
		}

		if(!($this->db = new SQLite3($sqliteDb))) {
			return false;
		}
		if (substr($this->plistFile, 0, 1) !== DIRECTORY_SEPARATOR) {
			$this->plistFile = __DIR__ . DIRECTORY_SEPARATOR . $this->plistFile;
		}
		copy($this->plistFile, $plistPath);

    	$this->db->query('CREATE TABLE searchIndex(id INTEGER PRIMARY KEY, name TEXT, type TEXT, path TEXT);');
    	$this->db->query('CREATE UNIQUE INDEX anchor ON searchIndex (name, type, path);');
    	foreach ($context->classes as $class)
    	{
    		$this->addType($class->name, 'Class', $this->generateFileName($class->name));
    		$this->typeHandle($class);
    	}

    	foreach ($context->traits as $trait)
    	{
    		$this->addType($trait->name, 'Trait', $this->generateFileName($trait->name));
    		$this->typeHandle($trait);
    	}

    	foreach ($context->interfaces as $interface)
    	{
    		$this->addType($interface->name, 'Interface', $this->generateFileName($interface->name));
    		$this->typeHandle($interface);
    	}

		return parent::render($context, $controller);
	}

	protected function typeHandle($type)
	{
		if (!empty($type->methods)) {
			foreach ($type->methods as $method) {
				if ($type->name != $property->definedBy) { continue; }
				$this->addType($type->name .'::'. $method->name, 'Method', $this->generateFileName($type->name) .'#'. $method->name . '()-detail');
			}
		}
		if (!empty($type->properties)) {
			foreach ($type->properties as $property) {
				if ($type->name != $property->definedBy) { continue; }
				$this->addType($type->name .'::'. $property->name, 'Property', $this->generateFileName($type->name) .'#'. $property->name . '-detail');
			}
		}

	}

	protected function addType($name, $type, $path)
	{
		return $this->db->query('INSERT OR IGNORE INTO searchIndex(name, type, path) VALUES (\''.SQLite3::escapeString($name).'\', \''.SQLite3::escapeString($type).'\', \''.SQLite3::escapeString($path).'\');');
	}
}