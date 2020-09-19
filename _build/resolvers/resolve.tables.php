<?php

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;
	$modelPath = $modx->getOption('lms.core_path', null, $modx->getOption('core_path') . 'components/lms/') . 'model/';

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modx->addPackage('lms', $modelPath);
			$manager = $modx->getManager();

			// Create or update new
			$tables = array(
				'Statistic',
			);

			foreach ($tables as $table) {
				$manager->createObjectContainer($table);
				$table_name = $modx->getTableName($table);
				// FIELDS
				$fields = array();
				$sql = $modx->query("SHOW FIELDS FROM {$table_name}");
				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
					if (strpos($row['Type'], 'int') === 0) {
						$type = 'integer';
					}
					else {
						$type = preg_replace('#\(.*#', '', $row['Type']);
					}
					$fields[$row['Field']] = strtolower($type);
				}
				// Add or alter existing fields
				$map = $modx->getFieldMeta($table);
				foreach ($map as $key => $field) {
					// Add new fields
					if (!isset($fields[$key])) {
						if ($manager->addField($table, $key)) {
							$modx->log(modX::LOG_LEVEL_INFO, "Added field \"{$key}\" in the table \"{$table}\"");
						}
					}
					else {
						$type = strtolower($field['dbtype']);
						if (strpos($type, 'int') === 0) {
							$type = 'integer';
						}
						// Modify existing fields
						if ($type != $fields[$key]) {
							if ($manager->alterField($table, $key)) {
								$modx->log(modX::LOG_LEVEL_INFO, "Updated field \"{$key}\" of the table \"{$table}\"");
							}
						}
					}
				}
				// Remove old fields
				foreach ($fields as $key => $field) {
					if (!isset($map[$key])) {
						if ($manager->removeField($table, $key)) {
							$modx->log(modX::LOG_LEVEL_INFO, "Removed field \"{$key}\" of the table \"{$table}\"");
						}
					}
				}
				// INDEXES
				$indexes = array();
				$sql = $modx->query("SHOW INDEXES FROM {$table_name}");
				while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
					$name = $row['Key_name'];
					if (!isset($indexes[$name])) {
						$indexes[$name] = array($row['Column_name']);
					}
					else {
						$indexes[$name][] = $row['Column_name'];
					}
				}
				foreach ($indexes as $name => $values) {
					sort($values);
					$indexes[$name] = implode(':', $values);
				}
				$map = $modx->getIndexMeta($table);
				// Remove old indexes
				foreach ($indexes as $key => $index) {
					if (!isset($map[$key])) {
						if ($manager->removeIndex($table, $key)) {
							$modx->log(modX::LOG_LEVEL_INFO, "Removed index \"{$key}\" of the table \"{$table}\"");
						}
					}
				}
				// Add or alter existing
				foreach ($map as $key => $index) {
					ksort($index['columns']);
					$index = implode(':', array_keys($index['columns']));
					if (!isset($indexes[$key])) {
						if ($manager->addIndex($table, $key)) {
							$modx->log(modX::LOG_LEVEL_INFO, "Added index \"{$key}\" in the table \"{$table}\"");
						}
					}
					else {
						if ($index != $indexes[$key]) {
							if ($manager->removeIndex($table, $key) && $manager->addIndex($table, $key)) {
								$modx->log(modX::LOG_LEVEL_INFO, "Updated index \"{$key}\" of the table \"{$table}\"");
							}
						}
					}
				}
			}

			if ($modx instanceof modX) {
				$modx->addExtensionPackage('lms', '[[++core_path]]components/lms/model/');
			}

			/*
			$level = $modx->getLogLevel();
			$modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);

			$manager->addField('TicketThread', 'comment_last');
			$manager->addIndex('TicketThread', 'comment_last');
			$manager->addField('TicketThread', 'comment_time');
			$manager->addIndex('TicketThread', 'comment_time');
			$manager->addField('TicketThread', 'comments');
			$manager->addIndex('TicketThread', 'comments');
			$manager->addField('TicketThread', 'closed');
			$manager->addIndex('TicketThread', 'closed');

			$manager->addField('TicketComment', 'raw');
			$manager->addField('TicketComment', 'properties');
			$manager->addField('TicketComment', 'published');
			$manager->addIndex('TicketComment', 'published');
			$manager->addField('TicketComment', 'rating');
			$manager->addIndex('TicketComment', 'rating');
			$manager->addField('TicketComment', 'rating_plus');
			$manager->addField('TicketComment', 'rating_minus');

			$manager->addField('TicketVote', 'owner');
			$manager->addIndex('TicketVote', 'owner');

			$manager->addField('TicketQueue', 'email');
			$manager->addIndex('TicketQueue', 'email');

			$manager->addField('TicketFile', 'thumbs');

			$manager->addField('TicketView', 'guest_key');
			$manager->removeIndex('TicketView', 'PRIMARY');
			$manager->addIndex('TicketView', 'unique_key');

			$modx->setLogLevel($level);
			*/
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			if ($modx instanceof modX) {
				$modx->removeExtensionPackage('lms');
			}
			break;
	}
}
return true;