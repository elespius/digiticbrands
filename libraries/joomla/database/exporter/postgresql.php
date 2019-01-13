<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * PostgreSQL export driver.
 *
 * @since       3.0.0
 * @deprecated  4.0  Use PDO PostgreSQL instead
 *
 * @property-read  JDatabaseDriverPostgresql  $db  The database connector to use for exporting structure and/or data.
 */
class JDatabaseExporterPostgresql extends JDatabaseExporter
{
	/**
	 * Builds the XML data for the tables to export.
	 *
	 * @return  string  An XML string
	 *
	 * @since   3.0.0
	 * @throws  Exception if an error occurs.
	 */
	protected function buildXml()
	{
		$buffer = array();

		$buffer[] = '<?xml version="1.0"?>';
		$buffer[] = '<postgresqldump xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
		$buffer[] = ' <database name="">';

		if ($this->options->withStructure)
		{
			$buffer = array_merge($buffer, $this->buildXmlStructure());
		}

		if ($this->options->withData)
		{
			$buffer = array_merge($buffer, $this->buildXmlData());
		}

		$buffer[] = ' </database>';
		$buffer[] = '</postgresqldump>';

		return implode("\n", $buffer);
	}

	/**
	 * Builds the XML structure to export.
	 *
	 * @return  array  An array of XML lines (strings).
	 *
	 * @since   3.0.0
	 * @throws  Exception if an error occurs.
	 */
	protected function buildXmlStructure()
	{
		$buffer = array();

		foreach ($this->from as $table)
		{
			// Replace the magic prefix if found.
			$table = $this->getGenericTableName($table);

			// Get the details columns information.
			$fields = $this->db->getTableColumns($table, false);
			$prefix = $this->db->getPrefix();
			$table_name = str_replace('#__', $prefix, $table);
			$keys = $this->db->getTableKeys($table_name);
			$sequences = $this->db->getTableSequences($table_name);

			$buffer[] = '  <table_structure name="' . $table . '">';

			if ($sequences)
			{
				foreach ($sequences as $sequence)
				{
					if (version_compare($this->db->getVersion(), '9.1.0') < 0)
					{
						$sequence->start_value = null;
					}

					$buffer[] = '   <sequence Name="' . $sequence->sequence . '"' . ' Schema="' . $sequence->schema . '"' .
						' Table="' . $sequence->table . '"' . ' Column="' . $sequence->column . '"' . ' Type="' . $sequence->data_type . '"' .
						' Start_Value="' . $sequence->start_value . '"' . ' Min_Value="' . $sequence->minimum_value . '"' .
						' Max_Value="' . $sequence->maximum_value . '"' . ' Last_Value="' . $this->db->getSequenceLastValue($sequence->sequence) . '"' .
						' Increment="' . $sequence->increment . '"' . ' Cycle_option="' . $sequence->cycle_option . '"' .
						' Is_called="' . $this->db->getSequenceIsCalled($sequence->sequence) . '"' .
						' />';
				}
			}

			foreach ($fields as $field)
			{
				$buffer[] = '   <field Field="' . $field->column_name . '"' . ' Type="' . $field->type . '"' . ' Null="' . $field->null . '"' .
							' Default="' . $field->Default . '"' . ' Comments="' . $field->comments . '"' .
					' />';
			}

			if ($keys)
			{
				foreach ($keys as $key)
				{
					$buffer[] = '   <key Index="' . $key->idxName . '"' . ' is_primary="' . $key->isPrimary . '"' . ' is_unique="' . $key->isUnique . '"' .
						' Key_name="' . $this->db->getNamesKey($table_name, $key->indKey) . '"' . ' Query=\'' . $key->Query . '\' />';
				}
			}

			$buffer[] = '  </table_structure>';
		}

		return $buffer;
	}

	/**
	 * Builds the XML data to export.
	 *
	 * @return  array  An array of XML lines (strings).
	 *
	 * @since   __DEPLOY_VERSION__
	 * @throws  Exception if an error occurs.
	 */
	protected function buildXmlData()
	{
		$buffer = array();

		foreach ($this->from as $table)
		{
			// Replace the magic prefix if found.
			$table = $this->getGenericTableName($table);

			// Get the details columns information.
			$fields  = $this->db->getTableColumns($table, false);
			$colblob = array();

			foreach ($fields as $field)
			{
				// Cacth blob for xml conversion
				// PostgreSQL binary large object type
				if ($field->Type == 'bytea')
				{
					$colblob[] = $field->Field;
				}
			}

			$query = $this->db->getQuery(true);
			$query->select($query->qn(array_keys($fields)))
				->from($query->qn($table));
			$this->db->setQuery($query);

			$rows = $this->db->loadObjectList();

			if (!count($rows))
			{
				continue;
			}

			$buffer[] = '  <table_data name="' . $table . '">';

			foreach ($rows as $row)
			{
				$buffer[] = '   <row>';

				foreach ($row as $key => $value)
				{
					if (!in_array($key, $colblob))
					{
						$buffer[] = '    <field name="' . $key . '">' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '</field>';
					}
					else
					{
						$buffer[] = '    <field name="' . $key . '">' . pg_unescape_bytea($value) . '</field>';
					}
				}

				$buffer[] = '   </row>';
			}

			$buffer[] = '  </table_data>';
		}

		return $buffer;
	}

	/**
	 * Checks if all data and options are in order prior to exporting.
	 *
	 * @return  JDatabaseExporterPostgresql  Method supports chaining.
	 *
	 * @since   3.0.0
	 * @throws  Exception if an error is encountered.
	 */
	public function check()
	{
		// Check if the db connector has been set.
		if (!($this->db instanceof JDatabaseDriverPostgresql))
		{
			throw new Exception('JPLATFORM_ERROR_DATABASE_CONNECTOR_WRONG_TYPE');
		}

		// Check if the tables have been specified.
		if (empty($this->from))
		{
			throw new Exception('JPLATFORM_ERROR_NO_TABLES_SPECIFIED');
		}

		return $this;
	}
}
