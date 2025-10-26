<?php

/**
 * Class wispgg_controller
 * Admin controller
 * @see https://dev.hostbillapp.com/dev-kit/advanced-topics/hostbill-controllers/
 * @author Xephia.eu
 */
class wispgg_controller extends HBController
{

    /**
     * Related module object (wispgg)
     * @var wispgg $module
     */
    var $module;

    /**
     * Admin authorization object
     * Use $this->authorization->get_id() - to get id of logged in staff member
     * @var AdminAuthorization
     */
    var $authorization;

    /**
     * Template object (subclass of Smarty).
     * Use it to assign variables to template
     * @var Smarty $template
     */
    var $template;

    public function productdetails($params)
    {
        $adminarea = APPDIR_MODULES
            . $this->module->getModuleType()
            . DS . strtolower($this->module->getModuleName())
            . DS . 'admin' . DS;

        $options = $this->module->getOptions();

        $connect = false;
        $s = HBLoader::LoadModel('Servers');
        if (isset($params['server_id'])) {
            $this->module->connect($s->getServerDetails($params['server_id']));
            if ($this->module->testConnection()) {
                $connect = true;
            }
        } elseif (isset($params['id'])) {
            $this->template->assign('product_id', $params['id']);
            $servers = $this->module->getProductServers($params['id']);
            if (is_array($servers)) {
                foreach ($servers as $server) {
                    if (empty($server))
                        continue;
                    $this->module->connect($s->getServerDetails($server));
                    if ($this->module->testConnection()) {
                        $connect = true;

                        break;
                    }
                }
            }
        }

        if ($connect)
            $this->template->assign('connect', 1);

        $this->template->assign('options', $options['simple']);
        $this->template->assign('customconfig', $adminarea . 'productconfig.tpl');
        $this->template->assign('module_templates', $adminarea);

        $this->template->assign('modulename', strtolower($this->module->getModuleName()));

        if (Controller::isAjax()) {
            $this->template->showtpl = "{$adminarea}ajax.product";
        }
    }
}
