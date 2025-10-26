<?php

/**
 * Class wispgg
 *
 * Hosting/Provisioning module
 *
 * @see DOCS TODO: Once Wisp decides they want to release a **DOCUMENTED** software, fill it in. (Probably never)
 *
 *
 * @see  http://dev.hostbillapp.com/dev-kit/provisioning-modules/
 * @author Xephia.eu
 *
 */
class wispgg extends HostingModule {

    use \Components\Traits\LoggerTrait;

    /**
     * @var string
     */
    protected $_repository = 'hosting_wispgg';

    /**
     * Module version. Make sure to increase version any time you add some
     * new functions in this class!
     * @var string
     */
    protected $version = '1.2023-08-31';

    /**
     * Module name, visible in admin portal.
     * @var string
     */
    protected $modname = 'Wisp.gg';

    /**
     * Module description, visible in admin portal
     * @var string
     */
    protected $description = 'Wisp.gg module for HostBill';

    /**
     * Connected instance of PDO object
     * @var PDO
     */
    protected $db;

    /**
     * You can choose which fields to display in Settings->Apps section
     * by defining this variable
     * @var array
     */
    protected $serverFields = [
        self::CONNECTION_FIELD_USERNAME => false,
        self::CONNECTION_FIELD_PASSWORD => false,
        self::CONNECTION_FIELD_INPUT1 => true,
        self::CONNECTION_FIELD_INPUT2 => false,
        self::CONNECTION_FIELD_CHECKBOX => true,
        self::CONNECTION_FIELD_HOSTNAME => true,
        self::CONNECTION_FIELD_IPADDRESS => false,
        self::CONNECTION_FIELD_MAXACCOUNTS => false,
        self::CONNECTION_FIELD_STATUSURL => false,
        self::CONNECTION_FIELD_TEXTAREA => false,
    ];

    /**
     * @var array
     */
    protected $serverFieldsDescription = [
        self::CONNECTION_FIELD_INPUT1 => 'Api Application Key',
    ];


    /**
     * options for the product configuration from Settings => Products & Services => Product => Connect with Module
     * @var array
     */
    protected $options = [

        'CPU' => [
            'value' => '',
            'description' => 'The amount of cpu limit you want the server to have',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'cpu',
            '_tab' => 'resources',
        ],
        'Disk Space' => [
            'value' => '',
            'description' => 'The amount of storage you want the server to use',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'disk',
            '_tab' => 'resources',
        ],
        'Disk Space Unit' => [
            'value' => 'MB',
            'description' => 'Unit for disk size set',
            'type' => 'select',
            'default' => ['MB','GB'],
            '_tab' => 'resources',
        ],
        'Memory' => [
            'value' => '',
            'description' => 'The amount of memory you want the server to use',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'memory',
            '_tab' => 'resources',
        ],
        'Memory Space Unit' => [
            'value' => 'MB',
            'description' => 'Unit for memory/swap size set',
            'type' => 'select',
            'default' => ['MB','GB'],
            '_tab' => 'resources',
        ],
        'Swap' => [
            'value' => '',
            'description' => 'The amount of memory you want the server to use',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'swap',
            '_tab' => 'resources',
        ],
        'Block IO Weight' => [
            'value' => '',
            'description' => 'The amount of memory you want the server to use',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'block_io_weight',
            '_tab' => 'resources',
        ],
        'Databases' => [
            'value' => '',
            'description' => 'The total number of databases a user is allowed to create for this server. Leave blank to allow unlimited',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'database',
            '_tab' => 'resources',
        ],
        'Dedicated IP' => [
            'value' => '',
            'description' => 'Check if you want the server to have a dedicated IP',
            'type' => 'check',
            'default' => '',
            'forms' => 'checkbox',
            'variable' => 'dedicated',
            '_tab' => 'resources',
        ],
        'Allocations' => [
            'value' => '',
            'description' => 'Number of allocations allowed',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'allocation',
            '_tab' => 'resources',
        ],
        'Backups' => [
            'value' => '',
            'description' => 'The server\'s backups limit',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'backups',
            '_tab' => 'resources',
        ],
        'Location' => [
            'value' => '',
            'description' => 'Locations that nodes can be assigned',
            'type' => 'loadable',
            'default' => 'getLocations',
            'forms' => 'select',
            'variable' => 'location',
            '_tab' => 'resources',
        ],
        'Port Range' => [
            'value' => '',
            'description' => '',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'port_range',
            '_tab' => 'resources',
        ],
        'Nest' => [
            'value' => '',
            'description' => 'Select the Nest that this server will be grouped under.',
            'type' => 'loadable',
            'default' => 'getNests',
            'forms' => 'select',
            'variable' => 'nest',
            '_tab' => 'nest',
        ],
        'Egg' => [
            'value' => '',
            'description' => 'Select the Egg that will define how this server should operate.',
            'type' => 'loadable',
            'default' => 'getEggs',
            'forms' => 'select',
            'variable' => 'egg',
            '_tab' => 'nest',
        ],
        'Egg variables' => [
            'value' => '',
            'description' => 'Put egg variables value, eg. variable:value;. You can also use $value to replace it with Component. Also use ${allocation} to pick from one of the allocations or ${port} for main port.',
            'type' => 'textarea',
            'default' => '',
            'forms' => 'input',
            'variable' => 'egg_variable',
            '_tab' => 'nest',
        ],
        'Docker Image' => [
            'value' => '',
            'description' => 'This is the default Docker image that will be used to run this server.',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'docker_image',
            '_tab' => 'nest',
        ],
        'Startup script' => [
            'value' => '',
            'description' => 'The following data substitutes are available for the startup command: {{SERVER_MEMORY}}, {{SERVER_IP}}, and {{SERVER_PORT}}. They will be replaced with the allocated memory, server IP, and server port respectively.',
            'type' => 'textarea',
            'default' => '',
            'forms' => 'input',
            'variable' => 'startup_script',
            '_tab' => 'nest',
        ],
        'Data Pack' => [
            'value' => '',
            'description' => '',
            'type' => 'input',
            'default' => '',
            'forms' => 'input',
            'variable' => 'data_pack',
            '_tab' => 'nest',
        ],
    ];

    /**
     * @var array
     */
    protected $details = [
        'device_id' => [
            'name' => 'device_id',
            'value' => false,
            'type' => 'input',
            'default' => false
        ],
        'username' => [
            'name' => 'username',
            'value' => false,
            'type' => 'input',
            'default' => false
        ],
        'password' => [
            'name' => 'password',
            'value' => false,
            'type' => 'input',
            'default' => false
        ],
        'domain' => [
            'name' => 'domain',
            'value' => false,
            'type' => 'input',
            'default' => false
        ],

    ];

    /**
     * @var
     */
    private $hostname;
    /**
     * @var
     */
    private $api_key;
    /**
     * @var
     */
    private $secure;
    /**
     * @var
     */
    private $response;
    /**
     * @var
     */
    private $response_code;

    /**
     * HostBill will call this method before calling any other function from your module
     * It will pass remote  app details that module should connect with
     *
     * @param array $connect Server details configured in Settings->Apps
     */
    public function connect($connect) {
        $this->hostname = $connect['host'];
        $this->api_key = $connect['field1'];
        $this->secure = $connect['secure'];
    }

    /**
     * HostBill will call this method when admin clicks on "test Connection" in settings->apps
     * It should test connection to remote app using details provided in connect method
     *
     * Use $this->addError('message'); to provide errors details (if any)
     *
     * @return boolean true if connection suceeds
     * @see connect
     */
    public function testConnection() {
        $check = $this->api('users');
        return $check !== false;
    }

    /**
     * @return string
     */
    function _parseHostname() {
        $hostname = $this->hostname;
        if (ip2long($hostname) !== false) $hostname = 'http://' . $hostname;
        else $hostname = ($this->secure ? 'https://' : 'http://') . $hostname;
        return rtrim($hostname, '/');
    }

    /**
     * @param $endpoint
     * @param string $method
     * @param array $data
     * @return bool|mixed
     */
    function api($endpoint, $method = "GET", $data = [], $expectedCode = 0) {
        $url = $this->_parseHostname() . '/api/admin/' . $endpoint;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $post = json_encode($data);

        $headers = [
            "Authorization: Bearer " . $this->api_key,
            "Accept: Application/vnd.pterodactyl.v1+json",
            "Content-Type: application/json",
        ];
        if ($method === 'POST' || $method === 'PATCH' || $method === 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
            $headers[] = "Content-Length: " . strlen($post);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        HBDebug::debug('HB ==> Wisp', [
            'headers' => $headers,
            'request body' => $post,
            'url' => $url,
            'method' => $method
        ]);
        $result = curl_exec($curl);
        $response = $this->response = json_decode($result, true);
        $code = $this->response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);

        HBDebug::debug('HB <== Wisp', [
            'result' => $result,
            'response body' => $response,
            'code' => $code,
            'curl err' => $err
        ]);

        if ($err) {
            $this->addError('Connection error ' . $err);
            return false;
        } else {
            if (isset($response['errors'])) {
                foreach ($response['errors'] as $error) {
                    $this->addError($error['code'] . ' detailss: ' . $error['detail']);
                    $this->addError('Endpoint: ' . $endpoint);
                    $this->addError('Method: ' . $method);
                    $this->addError('Data: ' . $post);
                    return false;
                }
            } else {
                return $response;
            }
        }
    }

    /**
     * @return bool
     */
    public function Create() {
        $egg = $this->getEgg($this->resource('nest'), $this->resource('egg'));
        $user = $this->getOrCreateUser();
        if (!$user) {
            $this->addError('Cannot create user');
            return false;
        }


        $mult_disk = $this->options['Disk Space Unit']['value'] == 'GB' ? 1000 : 1;
        $mult_mem = $this->options['Memory Space Unit']['value'] == 'GB' ? 1024 : 1;
        $mult_backups = 1000;

        $data = [];
        $data['oom_disabled'] = false;
        $data['owner_id'] = $user;
        $data['external_id'] = $this->account_details["id"];
        $data['name'] = $this->details['domain']['value'];
        //$data['nest'] = $this->resource('nest');
        $data['egg_id'] = $this->resource('egg');
        //$data['allocation_limit'] = $this->resource('allocation');
        $data['docker_image'] = !empty($this->resource('docker_image')) 
        ? $this->resource('docker_image') 
        : $egg['docker_image'];
        $data['startup'] = $this->resource('startup_script');
        /*$data['limits'] = [
            'memory' => $this->resource('memory') * $mult_mem,
            'swap' => $this->resource('swap') * $mult_mem,
            'disk' => $this->resource('disk') * $mult_disk,
            'io' => $this->resource('block_io_weight'),
            'cpu' => $this->resource('cpu'),
        ];*/
        $data['memory'] = $this->resource('memory') * $mult_mem;
        $data['swap'] = $this->resource('swap') * $mult_mem;
        $data['disk'] = $this->resource('disk') * $mult_disk;
        $data['io'] = $this->resource('block_io_weight');
        $data['cpu'] = $this->resource('cpu');
        /*$data['feature_limits'] = [
            'databases' => $this->resource('database'),
            'allocations' => $this->resource('allocation'),
            'backup_megabytes' => $this->resource('backups'),
        ];*/
        $data['database_limit'] = $this->resource('database');
        $data['allocation_limit'] = $this->resource('allocation');
        $data['backup_megabytes_limit'] = $this->resource('backups') * $mult_backups;
        $variables = $this->resource('egg_variable');
        if (!$variables) {
            $this->addError('Wrong or empty Egg variables');
            return false;
        }

        $nodeAndAllocations = $this->getNodeAndAllocations();
        if (!$nodeAndAllocations) {
            $this->addError('No suitable nodes with allocations');
            return false;
        }
        /*$data['deploy'] = [
            'locations' => [$this->resource('location')],
            'dedicated_ip' => $this->resource('dedicated') ? true : false,
            'port_range' => [$this->resource('port_range')]
        ];*/
        $data["node_id"] = $nodeAndAllocations["node"];
        $data["primary_allocation_id"] = $nodeAndAllocations["primary_allocation_id"][0];
        foreach ($nodeAndAllocations["secondary_allocation_ids"] as $idAndPort) {
            $data["secondary_allocation_ids"] = $idAndPort[0];
        }
        $data['start_on_completion'] = true;

        $data = $this->parseVariables($variables, $nodeAndAllocations, $data);

        $server = $this->api('servers', 'POST', $data);
        if (is_array($server)) {
            if (!$server['attributes']['id']) {
                $this->addError('Wrong or empty device ID');
                return false;
            }
            $this->details['device_id']['value'] = $server['attributes']['id'];
            return true;
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public function getNodeAndAllocations() {
        $allocation_count = $this->resource('allocation') + 1;
        $location_id = $this->resource('location');
        $location = $this->api('locations/' . $location_id . '?include=nodes');
        $nodes = $location['attributes']['relationships']['nodes']['data'];
        if (sizeof($nodes) < 1) {
            $this->addError('No nodes.');
            return false;
        }
        foreach ($nodes as $node) {
            $node_id = $node['attributes']['id'];
            $allocationsResponse = $this->api('nodes/' . $node_id . "/allocations?filter[in_use]=false");
            $allocations = $allocationsResponse['data'];
            if (sizeof($allocations) < $allocation_count) {
                continue;
            }

            $nodeAndAllocations = [];
            $nodeAndAllocations["node"] = $node_id;
            $count = 0;
            foreach ($allocations as $allocation) {
                $count++;
                if ($count > $allocation_count) {
                    break;
                }
                if ($count == 1) {
                    // [ID, PORT]
                    $nodeAndAllocations["primary_allocation_id"] = [$allocation['attributes']['id'], $allocation['attributes']['port']];
                    continue;
                }
                // [ID, PORT]
                $nodeAndAllocations["secondary_allocation_ids"][] = [$allocation['attributes']['id'], $allocation['attributes']['port']];
            }
            return $nodeAndAllocations;
        }
        $this->addError('No allocations.');
        return false;
    }

    /**
     * @return mixed
     */
    public function getOrCreateUser() {
        $user = $this->getUserId($this->client_data['id']);
        if (!$user) {
            $user_id = $this->createUser();
        } else {
            $q = $this->db->prepare(" SELECT id, username, password  FROM hb_accounts
                    WHERE client_id = :client_id AND server_id = :server_id
                    LIMIT 1 ");
            $q->execute(array(
                ':client_id' => $this->client_data['id'],
                ':server_id' => $this->account_details['server_id']
            ));
            $ret = $q->fetch(PDO::FETCH_ASSOC);
            $q->closeCursor();
            if (!$ret) {
                $user_id = $user['attributes']['id'];
            } else {
                $q = $this->db->prepare("UPDATE hb_accounts SET username = :username, password = :pass WHERE id = :id");
                $q->execute(array(
                    ':username' => $ret['username'],
                    ':pass' => $ret['password'],
                    ':id' => $this->account_details['id'],
                ));

                $this->details['username']['value'] = $ret['username'];
                $this->details['password']['value'] = Utilities::decrypt($ret['password']);

                $user_id = $user['attributes']['id'];
            }
        }
        return $user_id;
    }

    /**
     * @return mixed
     */
    private function createUser() {
        $userResult = $this->api('users/external/' . $this->client_data['id']);
        if ($this->response_code === 404) {
            $userResult = $this->api('users?filter[email]=' . urlencode($this->client_data['email']));
            if ($userResult['meta']['pagination']['total'] === 0) {

                // Set the correct initial language
                $language = $this->client_data["language"];
                $wisp_language = null;
                switch ($language) {
                    case "english":
                        $wisp_language = "en";
                        break;
                    case "czech":
                        $wisp_language = "cs_CZ";
                        break;
                    default:
                        $wisp_language = "en";
                }

                $userResult = $this->api('users', 'POST', [
                    'external_id' => $this->client_data['id'],
                    'username' => $this->details['username']['value'],
                    'password' => $this->details['password']['value'],
                    'email' => $this->client_data['email'],
                    'name_first' => $this->client_data['firstname'],
                    'name_last' => $this->client_data['lastname'],
                    'preferences' => ["language" => $wisp_language]
                ]);
            } else {
                foreach ($userResult['data'] as $key => $value) {
                    if ($value['attributes']['email'] === $this->client_data['email']) {
                        $userResult = array_merge($userResult, $value);
                        break;
                    }
                }
                $userResult = array_merge($userResult, $userResult['data'][0]);
            }
        }

        if (in_array($this->response_code, [200, 201])) {
            return $userResult['attributes']['id'];
        }

        Engine::addError('Failed to create user, received error code: ' . $userResult['status_code']);
        return false;
    }

    /**
     * @param $client_id
     * @return bool|mixed
     */
    public function getUserId($client_id) {
        return $this->api('users/external/' . $client_id);
    }


    /**
     * loadable
     *
     * @return array|bool
     */
    public function getLocations() {
        $locations = $this->api('locations');
        if (!$locations)
            return false;

        $locations_array = [];
        foreach ($locations['data'] as $location) {
            $locations_array[] = [$location['attributes']['id'], $location['attributes']['long']];
        }

        return $locations_array;
    }

    /**
     * loadable
     *
     * @return array|bool
     */
    public function getNests() {
        $nests = $this->api('nests');
        if (!$nests)
            return false;

        $nests_array = [];
        foreach ($nests['data'] as $nest) {
            $nests_array[] = [$nest['attributes']['id'], $nest['attributes']['name']];
        }

        return $nests_array;
    }

    /**
     * loadable
     *
     * @return array|bool
     */
    public function getEggs() {
        $eggs_array = [];
        try {
            $r = RequestHandler::singleton();
            $products = new Products();
            $product = $products->getProduct($r->getParam('id'));
            if ($product['options']['Nest']) {
                $eggs = $this->api('nests/' . $product['options']['Nest'] . '/eggs', 'GET');
                if (!$eggs)
                    return false;
            }
            foreach ($eggs['data'] as $egg) {
                $eggs_array[] = [$egg['attributes']['id'], 'Egg ' . $egg['attributes']['id']];
            }
        } catch (Exception $e) {

        }
        return $eggs_array;
    }

    /**
     * @param $nest_id
     * @param $egg_id
     * @return bool|mixed
     */
    public function getEgg($nest_id, $egg_id) {
        $egg = $this->api('nests/' . $nest_id . '/eggs/' . $egg_id);
        if (!$egg)
            return false;

        return $egg['attributes'];
    }

    /**
     * @return mixed
     */
    public function getServerDetails() {
        $details = $this->api('servers/' . $this->details['device_id']['value'] . '?include[]=node&include[]=nest&include[]=egg&include[]=allocations&include[]=user&include[]=features');
        return $details['attributes'];
    }

    /**
     * @return bool
     */
    public function Suspend() {
        $this->api('servers/' . $this->account_details['extra_details']['device_id'] . '/suspension', 'POST', [
            'suspended' => true
        ]);
        return in_array($this->response_code, [200, 204]);
    }

    /**
     * @return bool
     */
    public function Unsuspend() {
        $this->api('servers/' . $this->account_details['extra_details']['device_id'] . '/suspension', 'POST', [
            'suspended' => false
        ]);
        return in_array($this->response_code, [200, 204]);
    }

    /**
     * @return bool
     */
    public function Reinstall() {
        $this->api('servers/' . $this->account_details['extra_details']['device_id'] . '/reinstall', 'POST');
        return in_array($this->response_code, [200, 204]);
    }

    /**
     * @return bool
     */
    public function Rebuild() {
        $this->api('servers/' . $this->account_details['extra_details']['device_id'] . '/rebuild', 'POST');
        return in_array($this->response_code, [200, 204]);
    }

    /**
     * @return bool
     */
    public function Terminate() {
        $this->api('servers/' . $this->account_details['extra_details']['device_id'], 'DELETE');
        return in_array($this->response_code, [200, 204]);
    }

    /**
     * @return bool
     */
    public function ChangePackage() {
        $serv_details = $this->getServerDetails();
        $allocations = $serv_details['relationships']['allocations']['data'];

        $mult_disk = $this->options['Disk Space Unit']['value'] == 'GB' ? 1000 : 1;
        $mult_mem = $this->options['Memory Space Unit']['value'] == 'GB' ? 1024 : 1;
        $mult_backups = 1000;

        $this->api('servers/' . $this->account_details['extra_details']['device_id'] . '/build', 'PUT', [
            'allocation_id' => $this->getPrimaryAllocation($allocations),
            'memory' => $this->resource('memory') * $mult_mem,
            'swap' => $this->resource('swap') * $mult_mem,
            'disk' => $this->resource('disk') * $mult_disk,
            'io' => $this->resource('block_io_weight'),
            'cpu' => $this->resource('cpu'),
            'database_limit' => $this->resource('database'),
            'allocation_limit' => $this->resource('allocation'),
            'backup_megabytes_limit' => $this->resource('backups') * $mult_backups,
        ]);
        if (!in_array($this->response_code, [200, 204]))
            return false;

        $egg = $this->getEgg($this->resource('nest'), $this->resource('egg'));
        $data = [
            'egg_id' => $this->resource('egg'),
            'startup' => $this->resource('startup_script'),
            'docker_image' => $egg['docker_image'],
            'skip_scripts' => false
        ];

        $variables = $this->resource('egg_variable');
        if (!$variables) {
            $this->addError('Wrong or empty Egg variables');
            return false;
        }

        $nodeAndAllocations = $this->getNodeAndAllocations();
        if (!$nodeAndAllocations) {
            $this->addError('No suitable nodes with allocations');
            return false;
        }

        $data = $this->parseVariables($variables, $nodeAndAllocations, $data);

        $this->api('servers/' . $this->account_details['extra_details']['device_id'] . '/startup', 'PUT', $data);

        Engine::addError('Please note: Resource limits was updated!');
        return in_array($this->response_code, [200, 204]);
    }

    /**
     * Consumes array of Wisp's allocations and returns id of the one with primary set to true.
     * @param array $allocations of Wisp's allocations.
     * @return numeric|bool
     */
    public function getPrimaryAllocation($allocations) {
        foreach ($allocations as $allocation) {
            if ($allocation['attributes']['primary']) {
                return $allocation['attributes']['id'];
            }
        }
        return false;
    }

    /**
     * Components:Forms upgrade/downgrade.
     * if upgrade logic is in ChangePackage there is no need to edit function below.
     * @param array $account_config New account config, under $this->account_config there is old one
     * @return boolean
     */
    public function changeFormsFields($account_config) {
        if (empty($account_config))
            return true;

        $this->setAccountConfig(array_merge($this->account_config, $account_config));
        return $this->ChangePackage();
    }

    /**
     * @return string
     */
    public function getPanelLoginUrl() {
        return $this->_parseHostname() . '/login';
    }

    /**
     * @return array
     */
    public function getSynchInfo() {
        $info = $this->getServerDetails();
        $return = array();
        $this->details['domain']['value'] = $info['name'];
        $this->options['Memory']['value'] = $info['limits']['memory'];
        $return['suspended'] = $info['suspended'] /*? '1' : '0'*/;
        return $return;
    }

    /**
     * @param $product_id
     * @return array|bool
     */
    public function getProductServers($product_id) {
        if (empty($product_id)) {
            return false;
        }

        $query = $this->db->prepare("SELECT `server` FROM hb_products_modules WHERE `product_id` = :product_id");
        $query->execute(array('product_id' => $product_id));
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $query->closeCursor();

        if (!$result) {
            return false;
        }

        $servers = explode(',', $result['server']);

        return $servers;
    }

    /**
     * List domains managed by module.
     * Returned data is in form of an array with keys like in hb_accounts,
     *
     * @return array[] [
     *  'username' => 'username', //account identifier
     *
     *  '... other keys are optional'
     *  'account_id' => if its set and non-zero, it will mean that this account is alrady in HostBill. Module did some internal checks to determine this
     * ]
     */
    public function getAccounts() {
        $return = [];
        try {
            $servers = $this->api('servers/?include=user', 'GET');
            foreach ($servers['data'] as $server) {
                $server = $server['attributes'];
                $user = $server['relationships']['user']['attributes'];

                $l = [];
                $l['email'] = $user['email'];
                $l['username'] = $user['username'];
                $l['domain'] = $server['name'];
                $l['status'] = $server['suspended']/* ? 'Suspended' : 'Active'*/;
                $l['extra_details']['device_id'] = $server['id'];
                $l['extra_details']['domain'] = $server['name'];
                $return[] = $l;
            }
        } catch (Exception $e) {
            $this->logger()->error('Wisp error', [
                'message' => $e->getMessage(),
                'response' => $this->response,
                'response code' => $this->response_code,
                'line' => $e->getLine(),
            ]);
            $this->addError($e->getMessage());
        }
        return $return;
    }

    /**
     * TODO
     * Return type of import, one of:
     * - ImportAccounts_Model::TYPE_IMPORT_PRODUCTS
     * - ImportAccounts_Model::TYPE_IMPORT_NO_PRODUCTS  (most common use)
     * - ImportAccounts_Model::TYPE_IMPORT_HOSTNAMES
     * @return string
     */
    public function getImportType() {
        return ImportAccounts_Model::TYPE_IMPORT_NO_PRODUCTS;
    }

    function parseVariables($variables, $nodeAndAllocations, $data) {
        $nextAllocation = 0;
        $env = explode(';', $variables);
        foreach ($env as $ev) {
            $e = explode(':', $ev);
            if (isset($e[1])) {
                $val = trim($e[1]);
                preg_match_all("/\\\$([a-zA-Z_{}]*)/", $val, $match);
                foreach ($match[1] as $item) {
                    if ($item === '{allocation}') { // Assigning one of the allocations, if available.
                        $val = $nodeAndAllocations["secondary_allocation_ids"][$nextAllocation][1];
                        $val = "$val"; // To String
                        $nextAllocation++;
                    } else if ($item === '{port}') { // Assigning the main allocation.
                        $val = $nodeAndAllocations["primary_allocation_id"][1];
                        $val = "$val"; // To String
                    } else {
                        $val = str_replace("\$" . $item, $this->account_config[$item]["variable_id"], $val);
                        if ($val == null || $val == "") {
                            $val = $this->account_config[$item]["value"]; // If text-input.
                        }
                    }
                }
                $data['environment'][trim($e[0])] = $val;
            }
        }
        return $data;
    }
}
