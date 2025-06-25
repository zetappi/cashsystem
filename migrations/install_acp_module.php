<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        return isset($this->config['marcozp_cash_version']) && version_compare($this->config['marcozp_cash_version'], '1.0.0', '>=');
    }

    static public function depends_on()
    {
        return ['\phpbb\db\migration\data\v330\v330'];
    }

    public function update_data()
    {
        return [
            ['config.add', ['marcozp_cash_version', '1.0.0']],
            
            // Add main ACP module
            ['module.add', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_CASH'
            ]],
            
            // Add settings module
            ['module.add', [
                'acp',
                'ACP_CASH',
                [
                    'module_basename' => '\\marcozp\\cash\\acp\\main_module',
                    'modes' => ['settings'],
                ],
            ]],
        ];
    }
    
    public function revert_data()
    {
        return [
            // Rimuovi il modulo settings
            ['module.remove', [
                'acp',
                'ACP_CASH',
                [
                    'module_basename' => '\\marcozp\\cash\\acp\\main_module',
                    'modes' => ['settings'],
                ],
            ]],
            
            // Rimuovi la categoria principale
            ['module.remove', [
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_CASH'
            ]],
            
            ['config.remove', ['marcozp_cash_version']],
        ];
    }
}
