<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->startSetup();

if ($installer->getConnection()->isTableExists($installer->getTable('admin_role'))) {
    /**
     * Rename existing 'admin_role' table into 'authorization_role' (to avoid forcing Magento re-installation)
     * TODO: This conditional logic can be removed some time after pull request is delivered to the mainline
     */
    $installer->getConnection()->renameTable(
        $installer->getTable('admin_role'),
        $installer->getTable('authorization_role')
    );
} elseif (!$installer->getConnection()->isTableExists($installer->getTable('authorization_role'))) {
    /**
     * Create table 'authorization_role'
     */
    $table = $installer->getConnection()->newTable(
        $installer->getTable('authorization_role')
    )->addColumn(
        'role_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Role ID'
    )->addColumn(
        'parent_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Parent Role ID'
    )->addColumn(
        'tree_level',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Role Tree Level'
    )->addColumn(
        'sort_order',
        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Role Sort Order'
    )->addColumn(
        'role_type',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        1,
        ['nullable' => false, 'default' => '0'],
        'Role Type'
    )->addColumn(
        'user_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'User ID'
    )->addColumn(
        'user_type',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        16,
        ['nullable' => true, 'default' => null],
        'User Type'
    )->addColumn(
        'role_name',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        50,
        ['nullable' => true, 'default' => null],
        'Role Name'
    )->addIndex(
        $installer->getIdxName('authorization_role', ['parent_id', 'sort_order']),
        ['parent_id', 'sort_order']
    )->addIndex(
        $installer->getIdxName('authorization_role', ['tree_level']),
        ['tree_level']
    )->setComment(
        'Admin Role Table'
    );
    $installer->getConnection()->createTable($table);
}

if ($installer->getConnection()->isTableExists($installer->getTable('admin_rule'))) {
    /**
     * Rename existing 'admin_rule' table into 'authorization_rule' (to avoid forcing Magento re-installation)
     * TODO: This conditional logic can be removed some time after pull request is delivered to the mainline
     */
    $installer->getConnection()->renameTable(
        $installer->getTable('admin_rule'),
        $installer->getTable('authorization_rule')
    );
} elseif (!$installer->getConnection()->isTableExists($installer->getTable('authorization_rule'))) {
    /**
     * Create table 'authorization_rule'
     */
    $table = $installer->getConnection()->newTable(
        $installer->getTable('authorization_rule')
    )->addColumn(
        'rule_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
        'Rule ID'
    )->addColumn(
        'role_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        ['unsigned' => true, 'nullable' => false, 'default' => '0'],
        'Role ID'
    )->addColumn(
        'resource_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        255,
        ['nullable' => true, 'default' => null],
        'Resource ID'
    )->addColumn(
        'privileges',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        20,
        ['nullable' => true],
        'Privileges'
    )->addColumn(
        'permission',
        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        10,
        [],
        'Permission'
    )->addIndex(
        $installer->getIdxName('authorization_rule', ['resource_id', 'role_id']),
        ['resource_id', 'role_id']
    )->addIndex(
        $installer->getIdxName('authorization_rule', ['role_id', 'resource_id']),
        ['role_id', 'resource_id']
    )->addForeignKey(
        $installer->getFkName('authorization_rule', 'role_id', 'authorization_role', 'role_id'),
        'role_id',
        $installer->getTable('authorization_role'),
        'role_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )->setComment(
        'Admin Rule Table'
    );
    $installer->getConnection()->createTable($table);
}

$installer->endSetup();