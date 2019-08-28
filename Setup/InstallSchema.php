<?php
/**
* Copyright © 2016 Magento. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Ifthenpay\Payshop\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
    * {@inheritdoc}
    * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
    */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
          /**
          * Create table 'ifthenpay_payshop'
          */
          $connection = $setup->getConnection();
          
          if($connection->isTableExists('ifthenpay_payshop') != true) {
            $table = $connection
            ->newTable($setup->getTable('ifthenpay_payshop'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'id_transacao',
                Table::TYPE_TEXT,
                20,
                ['nullable' => false]
            )
            ->addColumn(
              'referencia',
              Table::TYPE_TEXT,
              13,
              ['nullable' => false]
            )
            ->addColumn(
              'validade',
              Table::TYPE_TEXT,
              8,
              ['nullable' => false]
                
            )
            ->addColumn(
              'order_id',
              Table::TYPE_INTEGER,
              100,
              ['nullable' => false]  
            )
            ->addColumn(
              'error',
              Table::TYPE_TEXT,
              250,
              ['nullable' => true]  
            );
            $setup->getConnection()->createTable($table);
          }
       
      }
}
